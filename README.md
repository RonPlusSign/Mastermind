# Mastermind game

## This is the classic Mastermind game
The game logic is written in **PHP**, that should run on a server. The client only views the page and sends the attempts at the server using the HTTP POST method. All the important data is stored in session.

To start the PHP server (for example using `localhost` and port 8000):
```
php -S localhost:8000 -t YOUR-PROJECT-FOLDER
```

![Mastermind example](MasterMind.PNG)