#!/bin/bash

###################################################
# Bash script to deploy code on development environment.
# Author: krishaweb

# Print message about deploying to development environment.
echo -e "\n\nDeploying project on ${DEV_ENV_IP} environment\n"

# SSH into the development server and execute commands remotely.
ssh -o StrictHostKeyChecking=no "${DEV_ENV_USER}@${DEV_ENV_IP}" -p ${DEV_ENV_PORT} bash <<EOF
# Check if root directory exists.
if [ -d "/var/www/wpress/skilltriks/wp-content/plugins/" ]; then
  echo "Root directory exists"
  cd /var/www/wpress/skilltriks/wp-content/plugins/
fi

# Check if project directory exists or not.
if [ ! -d skilltriks ]; then
  # Clone the repository if project directory does not exist
  git clone git@github.com:krishastore/skilltriks.git skilltriks
fi

# Change directory to the project directory.
cd skilltriks

# Switch to the development branch.
git checkout development

# Reset the repository to the latest commit.
# git reset --hard

# Pull the latest changes from the development branch.
git pull origin development

EOF