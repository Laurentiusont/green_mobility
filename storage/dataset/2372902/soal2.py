def main():
    #int bilangan
    suku = int(input('suku: '))
    awal = int(input('awal: '))
    increment = int(input('increment: '))
    operasi = int(input('operasi: '))
    
    #pengoprasian
    if operasi: 
        awal + suku * increment
    if operasi:
        awal - suku * increment
    else:
        awal * suku * increment
        print()
        
main()