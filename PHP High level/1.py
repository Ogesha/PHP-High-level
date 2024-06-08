import sqlite3
import random

class Init:
    __instance = None

    def __new__(cls, *args, **kwargs):
        if cls.__instance is None:
            cls.__instance = super(Init, cls).__new__(cls)
        return cls.__instance

    def __init__(self):
        self.connection = sqlite3.connect(':memory:')
        self.cursor = self.connection.cursor()
        self.create()
        self.fill()

    def __create(self):
        self.cursor.execute('''
            CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                script_name TEXT(25),
                start_time INTEGER,
                end_time INTEGER,
                result TEXT CHECK(result IN ('normal', 'illegal', 'failed', 'success'))
            )
        ''')
        self.connection.commit()

    def __fill(self):
        results = ['normal', 'illegal', 'failed', 'success']
        for _ in range(10):
            self.cursor.execute('''
                INSERT INTO test (script_name, start_time, end_time, result)
                VALUES (?, ?, ?, ?)
            ''', (
                f'script{random.randint(1, 100)}',
                random.randint(1000, 5000),
                random.randint(5001, 10000),
                random.choice(results)
            ))
        self.connection.commit()

    def get(self):
        self.cursor.execute('''
            SELECT * FROM test
            WHERE result IN ('normal', 'success')
        ''')
        return self.cursor.fetchall()

# Create instance and fetch data
init_instance = Init()
print(init_instance.get())
