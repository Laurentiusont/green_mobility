def main():
    # input suku
    suku = int(input("Jumlah Suku:"))
    awal = int(input("Awal:"))
    kenaikan = int(input("Incerment: "))
    operasi = input("+/*/- : ")
    
    awal_tambah = awal
    awal_kali = awal
    
    # operasi
    for i in range (0,suku+1,kenaikan):
        awal = int(input())
        if (operasi == "+"):
             awal_tambah += kenaikan
             print(f"Jumlah Deret: {awal_tambah}")
            
        if (operasi == "*" ):
            awal_kali *= kenaikan
            print(f"Hasil kali Deret : {awal_kali}")
            
            
    
    
        
    
    
    
main()