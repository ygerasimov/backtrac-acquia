#!/bin/sh

site="$1"
target_env="$2"
source_branch="$3"
deployed_tag="$4"
repo_url="$5"
repo_type="$6"

cd /var/www/html/${site}.${target_env}/hooks/backtrac

eval "./backtrac-deploy.sh $site $target_env $source_branch $deployed_tag $repo_url $repo_type"
