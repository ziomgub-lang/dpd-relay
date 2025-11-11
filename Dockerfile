FROM php:8.2-apache

# Skopiuj wszystkie pliki do katalogu serwera www
COPY . /var/www/html/

# Włącz mod_rewrite (na wszelki wypadek)
RUN a2enmod rewrite

# Ustaw plik startowy
RUN echo "DirectoryIndex relay.php" > /etc/apache2/conf-enabled/directoryindex.conf

# Otwórz port 80
EXPOSE 80

# Uruchom Apache
CMD ["apache2-foreground"]
