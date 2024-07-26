def main ():
    # Int Suku Awal
    s = int(input('Jumlah Suku : '))
    
    # Int Awal
    a = int(input('Awal : '))
    
    # Int Increment
    i = int(input('Increment : '))
    
    # Int Operasi
    opr = input('Operasi (+ / - / *) : ')
    
    # Variabel tambah
    total_tambah = 0
    
    # Variabel kurang
    total_kurang = a
    
    # Variabel kali
    total_kali = 1 
    
    print ('Deret: ')
    for i in range (0,s + 1,1):
        print (a, end = '')
        
        if (opr == '+'):
            if (i < s - 1):
                print ("+", end = '')
            total_tambah += a
            a = a + i
        elif (opr == '-'): 
             if (i < s - 1):
                print ("-", end = '')
             total_kurang -= a
             a = a - i
        elif (opr == '*'): 
             if (i < s - 1):
                print ("*", end = '')
             total_kali *= a
             a = a * i
             
        print ()
        if (opr == '+'):
            print (f'Jumlah Deret : {total_tambah}')
        elif (opr == '-'):
            print (f'Jumlah Deret : {total_kurang}')
        elif (opr == '*'):
            print (f'Jumlah Deret : {total_kali}')
    
main ()