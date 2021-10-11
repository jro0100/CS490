
# Last arg is the questionID
# Second to last arg is the current testcase number
# Third to last arg is the answer
# Fourth to last arg is the function name
# All other args are the parameters in order
if [ -z "$2" ]
then
  IFS=' '
  read -a arr <<< "$1"
else
  arr=("${@}")
fi

qid=${arr[-1]}
testnum=${arr[-2]}
ans=${arr[-3]}
funname=${arr[-4]}

file="${qid}/test.py"
paramstring="p0"

echo -en "\tdef test${testnum}(self):\n" >> "$file"
for (( i=0; i<${#arr[@]}-4; i++ ));
do
  echo "${arr[$i]}"
  echo -en "\t\tp${i} = ${arr[$i]}\n" >> "$file"
  if [ $i -gt 0 ]
  then
    paramstring+=", p${i}"
  fi
done

echo -en "\t\tans = ${ans}\n" >> "$file"
echo -en "\t\tself.assertEqual(${funname}(${paramstring}), ans)\n\n" >> "$file"
