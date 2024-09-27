import requests
import mysql.connector
import json

api_key = '4db686227dbc5c598e4e3432af5fa4d1'

api_urls = {
    'Genre': f'https://api.themoviedb.org/3/genre/movie/list?api_key={api_key}&language=en-US',
}

connection = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='MoviesApp'
)

cursor = connection.cursor()

cursor.execute('''
    CREATE TABLE IF NOT EXISTS Genre (
        id INT PRIMARY KEY,
        name VARCHAR(255)
    )
''')

def insert_Genre(Genre):
    cursor.execute('''INSERT INTO Genre(id, name) VALUES(%s, %s)
                   ON DUPLICATE KEY UPDATE
                    id=VALUES(id),
                    name=VALUES(name)''', (
                        Genre['id'],  # Sửa tại đây
                        Genre['name'],  # và tại đây
                    ))

for category, url in api_urls.items(): 
    response = requests.get(url)
    if response.status_code == 200:
        data = response.json()
        for Genre in data['genres']:  # Sửa tại đây
            insert_Genre(Genre)
    else:
        print(f"Failed to fetch data for {category}")
        
connection.commit()
cursor.close()
connection.close()
