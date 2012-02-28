if [ ! -n "$1" ]
then
  echo "Nope, response!"
  exit 65
fi
git init
git add .
git commit -m "$@"
git push -u origin master