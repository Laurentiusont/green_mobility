 # File : T04A_2372069.py
 # Penulis : Allegra Rochintaniawan Al-Nazhari
 # Tujuan Program : Menentukan angka itu genap atau tidak 

def main():
    
    while True :
        n = int(input("n :"))
        if n== 9999:
         break
        if n % 2 == 0:
            print(f"{n}\nGenap")
        else:
            print(f"{n}\nBukan Genap")

main()  