FROM bitnami/minideb-extras:stretch-r494
LABEL maintainer "Bitnami <containers@bitnami.com>"

# Install required system packages and dependencies
RUN install_packages cron libbz2-1.0 libc6 libcomerr2 libcurl3 libexpat1 libffi6 libfreetype6 libgcc1 libgcrypt20 libgmp10 libgnutls30 libgpg-error0 libgssapi-krb5-2 libhogweed4 libicu57 libidn11 libidn2-0 libjpeg62-turbo libk5crypto3 libkeyutils1 libkrb5-3 libkrb5support0 libldap-2.4-2 liblzma5 libmemcached11 libmemcachedutil2 libncurses5 libnettle6 libnghttp2-14 libp11-kit0 libpcre3 libpng16-16 libpq5 libpsl5 libreadline7 librtmp1 libsasl2-2 libsqlite3-0 libssh2-1 libssl1.0.2 libssl1.1 libstdc++6 libsybdb5 libtasn1-6 libtidy5 libtinfo5 libunistring0 libxml2 libxslt1.1 zlib1g
RUN bitnami-pkg unpack apache-2.4.41-2 --checksum 54e604bee81357824146780a26fbd99a889852240ae94cc4779264c9f231f535
RUN bitnami-pkg unpack php-7.2.24-0 --checksum 1b07c4047c4091aea0dbf93a36a7cbcedb84c6a57e65035fde48fa7ef7daafd3
RUN bitnami-pkg unpack mysql-client-10.2.29-0 --checksum 1576cfcf1dcf9d64f6c508fe2711716a0bcddceeb891ab9cd926eff9e9288990
RUN bitnami-pkg install libphp-7.2.24-0 --checksum 7c56993a3952525d70b1a81bd4a46abf31398452cdd6924a42e1d7e3f159989c
RUN bitnami-pkg unpack magento-2.3.3-0 --checksum ebaa59a538afba1730186bbb3a32ad316b3b5c72b3e456210fcae056fa1caf1a
RUN sed -i -e '/pam_loginuid.so/ s/^#*/#/' /etc/pam.d/cron
RUN find /opt/bitnami/magento/htdocs -type d -print0 | xargs -0 chmod 775 && find /opt/bitnami/magento/htdocs -type f -print0 | xargs -0 chmod 664 && chown -R bitnami:daemon /opt/bitnami/magento/htdocs

COPY rootfs /
ENV ALLOW_EMPTY_PASSWORD="no" \
    BITNAMI_APP_NAME="magento" \
    BITNAMI_IMAGE_VERSION="2.3.3-debian-9-r15" \
    ELASTICSEARCH_HOST="" \
    ELASTICSEARCH_PORT_NUMBER="" \
    EXTERNAL_HTTPS_PORT_NUMBER="443" \
    EXTERNAL_HTTP_PORT_NUMBER="80" \
    MAGENTO_ADMINURI="admin" \
    MAGENTO_DATABASE_NAME="bitnami_magento" \
    MAGENTO_DATABASE_PASSWORD="" \
    MAGENTO_DATABASE_USER="bn_magento" \
    MAGENTO_EMAIL="user@example.com" \
    MAGENTO_FIRSTNAME="FirstName" \
    MAGENTO_HOST="127.0.0.1" \
    MAGENTO_LASTNAME="LastName" \
    MAGENTO_MODE="developer" \
    MAGENTO_PASSWORD="bitnami1" \
    MAGENTO_USERNAME="user" \
    MARIADB_HOST="mariadb" \
    MARIADB_PORT_NUMBER="3306" \
    MARIADB_ROOT_PASSWORD="" \
    MARIADB_ROOT_USER="root" \
    MYSQL_CLIENT_CREATE_DATABASE_NAME="" \
    MYSQL_CLIENT_CREATE_DATABASE_PASSWORD="" \
    MYSQL_CLIENT_CREATE_DATABASE_PRIVILEGES="ALL" \
    MYSQL_CLIENT_CREATE_DATABASE_USER="" \
    PATH="/opt/bitnami/apache/bin:/opt/bitnami/php/bin:/opt/bitnami/php/sbin:/opt/bitnami/mysql/bin:/opt/bitnami/magento/bin:$PATH"

EXPOSE 80 443

ENTRYPOINT [ "/app-entrypoint.sh" ]
CMD [ "/run.sh" ]