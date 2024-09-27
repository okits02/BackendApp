import requests
import mysql.connector
import json
from concurrent.futures import ThreadPoolExecutor
import time

api_key = '4db686227dbc5c598e4e3432af5fa4d1'  # Thay thế bằng khóa API thực của bạn

api_urls = {
    'now_playing': f'https://api.themoviedb.org/3/movie/now_playing?api_key=e9e9d8da18ae29fc430845952232787c&language=en-US&page=1',
}

def get_video_link(movie_id):
    video_url_template = f'https://api.themoviedb.org/3/movie/{movie_id}/videos?api_key={api_key}'
    retries = 3
    for attempt in range(retries):
        try:
            response = requests.get(video_url_template, timeout=10)
            if response.status_code == 200:
                data = response.json()
                if 'results' in data and data['results']:
                    video_key = data['results'][0]['key']
                    return f'https://www.youtube.com/watch?v={video_key}'
        except requests.exceptions.RequestException as e:
            print(f"Error fetching video link for movie {movie_id}: {e}. Attempt {attempt + 1} of {retries}")
            time.sleep(2)  # Wait for 2 seconds before retrying
    return None

def create_table_if_not_exists(category, cursor, connection):
    cursor.execute(f'''
    CREATE TABLE IF NOT EXISTS {category} (
        id INT PRIMARY KEY,
        adult BOOLEAN,
        backdrop_path VARCHAR(255),
        genre_ids JSON,
        original_language VARCHAR(10),
        original_title VARCHAR(255),
        overview TEXT,
        popularity FLOAT,
        poster_path VARCHAR(255),
        release_date DATE,
        title VARCHAR(255),
        video BOOLEAN,
        vote_average FLOAT,
        vote_count INT,
        video_link VARCHAR(255)
    );
    ''')
    connection.commit()

def insert_movie(movie, category, cursor, connection):
    video_link = get_video_link(movie['id'])
    cursor.execute(f'''
    INSERT INTO {category} (id, adult, backdrop_path, genre_ids, original_language, original_title, overview, popularity, poster_path, release_date, title, video, vote_average, vote_count, video_link)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE 
        adult = VALUES(adult),
        backdrop_path = VALUES(backdrop_path),
        genre_ids = VALUES(genre_ids),
        original_language = VALUES(original_language),
        original_title = VALUES(original_title),
        overview = VALUES(overview),
        popularity = VALUES(popularity),
        poster_path = VALUES(poster_path),
        release_date = VALUES(release_date),
        title = VALUES(title),
        video = VALUES(video),
        vote_average = VALUES(vote_average),
        vote_count = VALUES(vote_count),
        video_link = VALUES(video_link)
    ''', (
        movie['id'],
        movie['adult'],
        movie['backdrop_path'],
        json.dumps(movie['genre_ids']),
        movie['original_language'],
        movie['original_title'],
        movie['overview'],
        movie['popularity'],
        movie['poster_path'],
        movie['release_date'],
        movie['title'],
        movie['video'],
        movie['vote_average'],
        movie['vote_count'],
        video_link
    ))
    connection.commit()  # Commit after each movie to avoid losing data if there's an error

def fetch_and_insert_movies(category, url, cursor, connection):
    retries = 3
    for attempt in range(retries):
        try:
            response = requests.get(url, timeout=10)
            if response.status_code == 200:
                data = response.json()
                create_table_if_not_exists(category, cursor, connection)  # Create table if not exists
                for movie in data.get('results', []):
                    insert_movie(movie, category, cursor, connection)
                break  # Break the loop if successful
            else:
                print(f"Failed to fetch data for {category}: {response.status_code}")
        except requests.exceptions.RequestException as e:
            print(f"Error fetching data for {category}: {e}. Attempt {attempt + 1} of {retries}")
            time.sleep(2)  # Wait for 2 seconds before retrying

try:
    connection = mysql.connector.connect(
        host='localhost',
        user='root',
        password='',
        database='MoviesApp'
    )

    with connection.cursor() as cursor, ThreadPoolExecutor() as executor:
        futures = [executor.submit(fetch_and_insert_movies, category, url, cursor, connection) for category, url in api_urls.items()]
        for future in futures:
            future.result()  # Wait for each future to complete

except mysql.connector.Error as err:
    print(f"MySQL Error: {err}")

except Exception as e:
    print(f"An error occurred: {e}")

finally:
    if 'connection' in locals() and connection.is_connected():
        connection.close()
