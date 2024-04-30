# Geavanceerd gebruikers analyse systeem

## Vereisten
Zorg ervoor dat [Docker](https://docs.docker.com/desktop/install/mac-install/) geïnstalleerd is op je systeem. Volg de installatiegids op Docker installeren.

## Installatie

### Docker containers opzetten
Ga naar de gekloonde directory:
`cd Gebruikers-analyse-systeem`


Plaats het bijgeleverde _.env_ bestand in de hoofdmap van het project. Deze is te vinden in de bijlagen. 

    Gebruikers-analyse-systeem > src


Start de Docker containers:
`docker-compose up -d --build app`

## Configuratie
Draai vervolgense deze commands:
- `docker-compose run --rm composer update`
- `docker-compose run --rm npm run dev`
- `docker-compose run --rm artisan migrate`
- `docker-compose run --rm artisan db:seed`
- `docker-compose run --rm artisan key:generate`
- `docker-compose up scheduler`


## Final
Dat is de installatie compleet! Er kan ingelogd worden met de volgende gegevens: 

    admin@admin.com
    secret

Voor een beter zicht naar de werken met verbeterede dummy data, zie de bijgeleverde video "Realisatie product DEMO" bij de bijlagen.
