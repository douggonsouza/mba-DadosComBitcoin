FROM prashu321/ubuntuapache2

# Configurações do apache
#COPY ./docker/dev.conf /etc/httpd/conf.d/dev.conf

WORKDIR /var/www/html/

EXPOSE 80 443

#CMD ["/etc/init.d/apache2", "-DFOREGROUND"]
CMD ["/usr/sbin/apachectl", "-DFOREGROUND"]
