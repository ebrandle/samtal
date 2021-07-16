#Code and language creator: E. Brandle
#Restarted and brought to GitHub: 2021/07/15


##################
''' TO DO LIST '''
##################
'''
+ save english & samtal translations of words
+ list all words or all groups
- search for translation of word in either direction
+ export database
- add words to various groups (noun, verb, weather, etc)
- list all words in a group (tambal alphabetical order)
- list all groups for a certain word
- delete word/group
- update word/group
- find rhymes
'''

###################
''' INTRO STUFF '''
###################
import sqlite3
import csv

menutext = """
0) Exit program
A) Add word or part of speech
L) List words or available parts of speech
S) Search for word translation
X) Export dictionary to samtal_dictionary.csv
"""

connection = sqlite3.connect('samtal.db')
cursor = connection.cursor()


###########
''' ADD '''
###########
def add():
    addType = input('\nDo you want to add a word or part of speech (pick one): ').lower()
    if addType == 'word' or addType == 'words' or addType == 'w':
        add_word()
    elif addType == 'pos' or addType == 'part of speech' or addType == 'p':
        add_pos()
    else:
        print('Invalid table name. Please try again.')

def add_word():
    sam = input('>>> Samtal: ').lower()
    eng = input('>>> English: ').lower()
    eng2 = input('>>> Optional 2nd translation: ').lower()
    values = (sam, eng, eng2)
    cursor.execute('INSERT INTO words (samtal, english, eng_def_2) VALUES (?,?,?)', values)
    connection.commit()

def add_pos():
    p = input('>>> Part of speech: ').lower()
    values = (p,)
    cursor.execute('INSERT INTO part_of_speech (pos) VALUES (?)', values)
    connection.commit()


############
''' LIST '''
############
def list_stuff():
    listType = input('\nDo you want to list a word or part of speech (pick one): ').lower()
    if listType == 'word' or listType == 'words' or listType == 'w':
        list_words()
    elif listType == 'pos' or listType == 'part of speech' or listType == 'p':
        list_pos()
    else:
        print('Invalid table name. Please try again.')

def list_words():
    print("\n<< Samtal: English >>")
    for row in cursor.execute('SELECT * FROM words'):
        tmpRow = row[0] + ': ' + row[1]
        if row[2] != '':
            tmpRow = tmpRow + ', ' + row[2]
        print(tmpRow)

def list_pos():
    print("\nCurrent parts of speech")
    for row in cursor.execute('SELECT * FROM part_of_speech'):
        tmpRow = row[0]
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
        for row in cursor.execute('SELECT samtal,english FROM words where english=?',value):
            print('\nIn Samtal,',row[1],'is',row[0]+'.')

    # samtal
    elif lang == 's' or lang == 'sam' or lang == 'samtal':
        for row in cursor.execute('SELECT samtal,english FROM words where samtal=?',value):
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
    elif selection == 'ap':
        add_pos()
    
    # list
    elif selection == 'l':
        list_stuff()
    elif selection == 'lw':
        list_words()
    elif selection == 'lp':
        list_pos()

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

CREATE TABLE part_of_speech (
    pos text PRIMARY KEY
);

CREATE TABLE link_words_pos (
    samtal_link text NOT NULL,
    pos_link text NOT NULL,
    FOREIGN KEY (samtal_link) REFERENCES words (samtal),
    FOREIGN KEY (pos_link) REFERENCES part_of_speech (pos)
);
'''

###########
''' TMP '''
###########
'''
    elif selection == '3':
        add_word_to_pos()
        
    elif selection == '4':
        list_words_in_pos()

    elif selection == 's':
        search_word()

    elif selection == 'd':
        delete()
'''
