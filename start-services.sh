#!/bin/bash

# Iniciar MySQL
service mysql start

# Iniciar Apache em primeiro plano
apache2ctl -D FOREGROUND


