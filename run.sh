#!/bin/bash
set -eo pipefail

function dev-init {
	composer install && cd tools && composer install
}

function dev {
	yarn run dev
}

function release {
	echo "Generating build directory..."
	rm -rf "$(pwd)/release"
	mkdir -p "$(pwd)/release"

	echo "Syncing files..."
	rsync -rc --exclude-from="$(pwd)/.distignore" "$(pwd)/" "$(pwd)/release/shadcn" --delete --delete-excluded

	echo "Generating zip file..."
	cd release/

	zip -q -r "shadcn.zip" "shadcn/"
	rm -rf shadcn
	echo "Generated release file"

	echo "Release successfully"
}

function help {
  printf "%s <task> [args]\n\nTasks:\n" "${0}"

  compgen -A function | grep -v "^_" | cat -n
}

TIMEFORMAT=$'\nTask completed in %3lR'
time "${@:-help}"