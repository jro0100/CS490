
# Adds the closing line to the test file in a given question directory
file="$1/test.py"
echo -en "if __name__ == '__main__':\n\tunittest.main()\n" >> "$file"
