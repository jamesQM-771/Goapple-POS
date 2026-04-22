import hashlib

texto = "gio123"
hash_resultado = hashlib.sha256(texto.encode()).hexdigest()

print(hash_resultado)