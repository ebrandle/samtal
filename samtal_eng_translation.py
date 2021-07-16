#Code and language creator: E. Brandle
#Restarted and brought to GitHub: 2021/07/15


##################
''' TO DO LIST '''
##################
'''
+ save english & samtal translations of words
+ list all words or all groups
+ search for translation of word in either direction
+ export database
+ add words to various categories (noun, verb, weather, etc)
- list all words in a category
- list all categories for a certain word
- delete word/category
- update word/category
- find rhymes
'''

###################
''' INTRO STUFF '''
###################
import sqlite3
import csv

menutext = """
0) Exit program
A) Add word or category
C) Categorize word
L) List words or available categories
S) Search for word translation
X) Export dictionary to samtal_dictionary.csv
"""

connection = sqlite3.connect('samtal.db')
cursor = connection.cursor()


###########
''' ADD '''
###########
def add():
    addType = input('\nDo you want to add a word or category (pick one): ').lower()
    if addType == 'word' or addType == 'words' or addType == 'w':
        add_word()
    elif addType == 'cat' or addType == 'category' or addType == 'c':
        add_cat()
    else:
        print('Invalid table name. Please try again.')

def add_word():
    sam = input('>>> Samtal: ').lower()
    eng = input('>>> English: ').lower()
    eng2 = input('>>> Optional 2nd translation: ').lower()
    values = (sam, eng, eng2)
    cursor.execute('INSERT INTO words (samtal, english, eng_def_2) VALUES (?,?,?)', values)
    connection.commit()

def add_cat():
    c = input('>>> category: ').lower()
    values = (c,)
    cursor.execute('INSERT INTO categories (cat) VALUES (?)', values)
    connection.commit()


##################
''' CATEGORIZE '''
##################
def categorize_word():
    # choose word
    lstWords = input('>>> List all available words? ').lower()
    if lstWords == 'y' or lstWords == 'yes':
        list_words()
        print()
    wordToAdd = input('>>> Word to categorize: ').lower()
    
    # word validation
    wordLs = []
    for row in cursor.execute('SELECT * FROM words'):
        wordLs.append(row[0])
    if wordToAdd not in wordLs:
        print("\n!! Word validation error !!")
        return

    # choose category
    lstCat = input('\n>>> List all available categories? ').lower()
    if lstCat == 'y' or lstCat == 'yes':
        list_cat()
        print()
    catToAddTo = input('>>> Category to add to: ').lower()

    # cat validation
    catLs = []
    for row in cursor.execute('SELECT * FROM categories'):
        catLs.append(row[0])
    if catToAddTo not in catLs:
        print("\n!! Category validation error !!")
        return
    
    values = (wordToAdd, catToAddTo,)
    cursor.execute('INSERT INTO link_words_cat (samtal_link, cat_link) VALUES (?, ?)', values)
    connection.commit()


############
''' LIST '''
############
def list_stuff():
    listType = input('\nDo you want to list words or categories? ').lower()
    if listType == 'w' or listType == 'word' or listType == 'words':
        list_words()
    elif listType == 'c' or listType == 'cat' or listType == 'categories':
        list_cat()
    else:
        print('Invalid table name. Please try again.')

def list_words():
    print("\n<< Samtal: English >>")
    for row in cursor.execute('SELECT * FROM words'):
        tmpRow = row[0] + ': ' + row[1]
        if row[2] != '':
            tmpRow = tmpRow + ', ' + row[2]
        print(tmpRow)

def list_cat():
    print("\n<< Current categories >>")
    for row in cursor.execute('SELECT * FROM categories'):
        print(row[0])

def list_link():
    print("\n<< List of links >>")
    for row in cursor.execute('SELECT * FROM link_words_cat'):
        print(row[0],row[1])

def list_by_cat():
    # choose category
    lstCat = input('\n>>> List all available categories? ').lower()
    if lstCat == 'y' or lstCat == 'yes':
        list_cat()
        print()
    cat = input('\nCateogory to list words from: ').lower()

    # category validation
    catLs = []
    for row in cursor.execute('SELECT * FROM categories'):
        catLs.append(row[0])
    if cat not in catLs:
        print("\n!! Invalid category !!")
        return

    # find words in category
    print('\n<<',cat.upper(),'>>')
    catVal = (cat,)
    for link_row in cursor.execute('SELECT * FROM link_words_cat WHERE cat_link=?',catVal):
        print('link row:',link_row)
        linkVal = (link_row[0],)
        for row in cursor.execute('SELECT * FROM words WHERE samtal=?',linkVal):
            print('row:',row)
            tmpRow = row[0] + ': ' + row[1]
            if row[2] != '':
                tmpRow = tmpRow + ', ' + row[2]
            print(tmpRow)


##############
''' SEARCH '''
##############
def look_up():
    lang = input('Search by language: ')
    word = input('Word to search: ')
    value = (word,)

    # english
    if lang == 'e' or lang == 'eng' or lang == 'english':
        for row in cursor.execute('SELECT samtal,english FROM words WHERE english=?',value):
            print('\nIn Samtal,',row[1],'is',row[0]+'.')

    # samtal
    elif lang == 's' or lang == 'sam' or lang == 'samtal':
        for row in cursor.execute('SELECT samtal,english FROM words WHERE samtal=?',value):
            print(row[0],'means',row[1])
    else:
        print('\nInvalid language code.')


##############
''' EXPORT '''
##############
def export_csv():
    with open('samtal_dictionary.csv', 'w', newline='') as csvfile:
        fieldnames = ['samtal', 'english', 'eng_2']
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        writer.writeheader()
        for row in cursor.execute('SELECT * FROM words'):
            writer.writerow({'samtal': row[0], 'english': row[1], 'eng_2': row[2]})

    print('CSV export to "samtal_dictionary.csv" complete.')

def export_dict():
    fullDict = {}
    f = open('samtal_dictionary.txt','w')
    for row in cursor.execute('SELECT * FROM words'):
        fullDict[row[0]] = {'Samtal': row[0], 'English': row[1], 'Eng_2': row[2]}
    f.write(str(fullDict))
    f.close()
    
    print('Dictionary export to "samtal_dictionary.txt" complete.')


######################
''' CALL FUNCTIONS '''
######################
while True:
    print(menutext)
    selection = input('>>> ').lower()

    # exit
    if selection == '0' or selection == 'q':
        break

    # add
    elif selection == 'a':
        add()
    elif selection == 'aw':
        add_word()
    elif selection == 'ac':
        add_cat()

    # categorize
    elif selection == 'c':
        categorize_word()
    
    # list
    elif selection == 'l':
        list_stuff()
    elif selection == 'lw':
        list_words()
    elif selection == 'lc':
        list_cat()
    elif selection == 'lbc':
        list_by_cat()

    # find
    elif selection == 's':
        look_up()

    # export
    elif selection == 'x':
        export_csv()
        export_dict()
    elif selection == 'xc':
        export_csv()
    elif selection == 'xd':
        export_dict()
    elif selection == 'xt':
        export_dict()
        
    else:
        print('\nInvalid menu selection. Please try again.')


#######################
''' DATABASE FORMAT '''
#######################
'''
CREATE TABLE words (
    samtal text PRIMARY KEY,
    english text NOT NULL,
    eng_def_2 text
);

CREATE TABLE categories (
    cat text PRIMARY KEY
);

CREATE TABLE link_words_cat (
   samtal_link text NOT NULL,
   cat_link text NOT NULL,
   FOREIGN KEY (samtal_link) REFERENCES words (samtal),
   FOREIGN KEY (cat_link) REFERENCES categories (cat)
   );
'''

###########
''' TMP '''
###########
