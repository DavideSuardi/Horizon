# Usa l'immagine PHP ufficiale con FPM
FROM php:8.2-fpm

# Installa le dipendenze necessarie
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    redis-server \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip \
    && apt-get clean

# Installa Redis per PHP (assicurati di avere l'estensione Redis correttamente configurata)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Assicurati che il server Redis funzioni correttamente
RUN service redis-server start

# Configurazione del progetto
WORKDIR /var/www

# Copia il tuo progetto nella cartella di lavoro
COPY . .

# Installa Composer (se necessario)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Espone la porta del container
EXPOSE 9000
