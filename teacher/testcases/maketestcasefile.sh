
# Generates the directory and beginning of the unit test file for a given question ID and function name
if [ -z "$2" ]
then
  IFS=' '
  read -a arr <<< "$1"
  qid=${arr[0]}
  funname=${arr[1]}
else
  qid="$1"
  funname="$2"
fi

mkdir "$qid"
mkdir "$qid"/studentanswer

echo -en "import unittest\nfrom studentanswer import" "$funname" "\nclass Tests(unittest.TestCase):\n" > "$qid"/test.py

