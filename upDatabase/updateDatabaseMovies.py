import pymysql
import requests

# Kết nối đến cơ sở dữ liệu MySQL
db = pymysql.connect(host="localhost", user="root", password="", database="moviesapp")
cursor = db.cursor()

# Thêm cột mới vào bảng movies
add_column_query = """
    ALTER TABLE movies
    ADD COLUMN runtime INT AFTER release_date,
    ADD COLUMN production_companies VARCHAR(255) AFTER runtime
"""
cursor.execute(add_column_query)
db.commit()

# Lấy danh sách các bộ phim từ cơ sở dữ liệu
select_query = "SELECT id FROM movies"
cursor.execute(select_query)
movies = cursor.fetchall()

# Lặp qua từng bộ phim và cập nhật dữ liệu từ API
for movie in movies:
    movie_id = movie[0]
    api_url = f"https://api.themoviedb.org/3/movie/{movie_id}?api_key=4db686227dbc5c598e4e3432af5fa4d1&append_to_response=videos"

    # Gọi API để lấy dữ liệu
    response = requests.get(api_url)
    if response.status_code == 200:
        movie_data = response.json()

        # Trích xuất thông tin runtime và production_companies từ dữ liệu API
        runtime = movie_data.get('runtime')
        production_companies = movie_data.get('production_companies')
        production_companies_logo_path = None
        if production_companies:
            production_companies_logo_path = production_companies[0].get('logo_path')

        # Cập nhật dữ liệu vào cơ sở dữ liệu
        update_query = f"UPDATE movies SET runtime = {runtime}, production_companies = '{production_companies_logo_path}' WHERE id = {movie_id}"
        cursor.execute(update_query)
        db.commit()

# Đóng kết nối
db.close()
