FROM mysql:8.0-debian

RUN echo '-= BUILD MYSQL IMAGE =-'

# Remove incorrect GPG key
USER root
RUN rm /etc/apt/sources.list.d/mysql.list
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 467B942D3A79BD29

RUN apt-get update && apt-get install -y --no-install-recommends vim \
    && apt-get clean && apt-get autoremove -y

RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set timezone
RUN rm /etc/localtime && \
    ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime && \
    date
