#!/bin/python
import sys
import base64
from cryptography.hazmat.backends import default_backend
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC

password = sys.argv[1].encode()
salt = b"sw\xea\x01\x9d\x109\x0eF\xef/\n\xb0mWK"
kdf = PBKDF2HMAC(
    algorithm=hashes.SHA256,
    length=32,
    salt=salt,
    iterations=10000,
    backend=default_backend(),
)
print(base64.urlsafe_b64encode(kdf.derive(password)).decode())
