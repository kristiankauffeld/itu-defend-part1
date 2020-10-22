import json, sqlite3, click, functools, os, hashlib
from flask import Flask, current_app, g, session, redirect, render_template, url_for, request




### DATABASE FUNCTIONS ###

def connect_db():
    return sqlite3.connect(app.database)

def init_db():
    """Initializes the database with our great SQL schema"""
    conn = connect_db()
    db = conn.cursor()
    db.executescript("""
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS notes;

CREATE TABLE notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    assocUser INTEGER NOT NULL,
    dateWritten DATETIME NOT NULL,
    note TEXT NOT NULL
);

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL
);

INSERT INTO users VALUES(null,"admin", "password");
INSERT INTO users VALUES(null,"bernardo", "omgMPC");
""")



### APPLICATION SETUP ###
app = Flask(__name__)
app.database = "db.sqlite3"
app.secret_key = os.getrandom(32)

### ADMINISTRATOR'S PANEL ###
def login_required(view):
    """Checks that the administrator has logged in, if so it returns the requested view, otherwise
    redirects to the login page"""
    @functools.wraps(view)
    def wrapped_view(**kwargs):
        if not session.get('logged_in'):
            return redirect(url_for('login'))
        return view(**kwargs)
    return wrapped_view

@app.route("/")
@login_required
def index():
     return f"""<!DOCTYPE html>
<html>
    <head><title>CoviDIoT - Overview </title></head>
    <body><h1>CoviDIoT - Overview</h1>
    <h1>Hello to you {f''+session['username']}</h1>
        <h2>Notes list:</h2>
        <table>
        <tr><th>Device ID</th><th>Key</th><th>Registration date</th><th>Firmware ID</th></tr>
        </table>
        <h2>Maybe infected:</h2>
        <table>
        <tr><th>Device ID</th><th>Proximity</th><th>Time exposed</th></tr>
        </table>
        <h2>Upload new firmware:</h2>
        <form method=POST action=/upload_firmware/ enctype=multipart/form-data>
        <input type=text name=code/>
        <button type=submit value=Upload/>
        </form>
        <div><a href="/logout/">Logout</a>
    </body>
</html>"""


@app.route("/login/", methods=('GET', 'POST'))
def login():
    message = ""
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        db = connect_db()
        c = db.cursor()
        statement = "SELECT * FROM users WHERE username = '%s' AND password = '%s';" %(username, password)
        c.execute(statement)
        result = c.fetchall()
        if len(result) > 0:
            session.clear()
            session['logged_in'] = True
            session['userid'] = result[0][0]
            session['username']=result[0][1]
            return redirect(url_for('index'))
        else:
            message = "Wrong username or password!"
    return f"""<!DOCTYPE html>
<html>
    <head><title>CoviDIoT - Login </title></head>
    <body>
        <h1>CoviDIoT - Login</h1>
        {message}
        <form method=POST>
            Username: <input type="text" name="username"/><br/>
            Password: <input type="password" name="password"/><br/>
            <button>Login</button>
        </form>
    </body>
</html>"""

@app.route("/logout/")
@login_required
def logout():
    """Logout: clears the session"""
    session.clear()
    return redirect(url_for('index'))

if __name__ == "__main__":
    #create database if it doesn't exist yet
    if not os.path.exists(app.database):
        init_db()

    app.run(host='0.0.0.0') # runs on machine ip address to make it visible on netowrk