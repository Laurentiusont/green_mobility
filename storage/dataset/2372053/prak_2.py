def main():
    #int jumlah suku
    suku = int(input('Jumlah Suku: '))
    #int awal
    awal = int(input('Awal: '))
    #int increment
    inc = int(input('Increment: '))
    #int operasi 
    opr = str(input('tambah/kurang/bagi: '))
    #int jumlah
    jumlah = 0
    
    for i in range (awal , suku , +inc):
        if opr == 'tambah':
            jumlah = jumlah + inc
        elif opr == 'kurang':
            jumlah = jumlah - inc
        elif opr == 'bagi':
            jumlah = jumlah // inc
    print (jumlah)
        
    
    
main()