import spotipy
from spotipy.oauth2 import SpotifyClientCredentials
import csv

# Configurer les identifiants de l'application Spotify
sp = spotipy.Spotify(auth_manager=SpotifyClientCredentials(client_id="fa78e4f32711481584efc8ea0022a54e", client_secret="1774acd3923c4701bd0914a265ad1634"))

# ID de la playlist
playlist_id = "spotify:playlist:0TSUk5xSQUX1CAqsjyEipE"

# Récupérer les détails de la playlist
results = sp.playlist_tracks(playlist_id)
tracks = results['items']

# Écrire les détails de la playlist dans un fichier CSV
with open('playlist.csv', mode='w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow(['titre', 'artiste', 'album', 'année', 'genre'])  # En-têtes de colonnes

    for item in tracks:
        track = item['track']
        titre = track['name']
        artiste = track['artists'][0]['name']
        album = track['album']['name']
        annee = track['album']['release_date'][:4]
        genre = "Unknown"  # Spotify ne donne pas toujours le genre directement
        writer.writerow([titre, artiste, album, annee, genre])
