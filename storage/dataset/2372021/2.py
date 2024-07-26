def main ():
    jumlah_suku = int(input("Jumlah suku : "))
    awal = int(input("Awal : "))
    increament = int(input("Increment : "))
    operasi = input("+ - x")
    akhir_tambah = 0
    akhir_kali = 1 
    akhir_kurang = 0
    
    if (operasi == "+"):
        hasil_tambah = jumlah_suku * increament + 1 
        for x in range (awal , hasil_tambah , increament):  
            akhir_tambah = akhir_tambah + x          
            print (f"Jumlah deret : {akhir_tambah}") 
        
    elif (operasi == "x"):
        hasil_kali = jumlah_suku * increament + 1
        for x in range (awal , hasil_kali + 1 , increament):
            akhir_kali = akhir_kali * x
            print (f"Hasil kali x deret {akhir_kali}")
            
    else:
        hasil_kurang = jumlah_suku - increament
        for x in range (awal , hasil_kurang - 1 , increament):   
            x = x - increament
            akhir_kurang = akhir_kurang - x
            print (f"Akhir kurang deret {akhir_kurang}")
            
main ()