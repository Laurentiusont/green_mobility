def main():
    #int suku
    suku = int(input('Jumlah suku : '))
    
    #int awal
    awal = int(input('Awal : '))
    
    #int increment
    increment = int(input('Increment : '))
    
    #string operasiStr
    operasiStr = str(input('Operasi : '))
    
    
    for i in range((awal + increment), suku==0 , +increment):
        suku = suku - 1
        string = str(awal) + operasiStr + str(i)
        total = awal + i
        print('Deret: ')
        print(string)
    
main()