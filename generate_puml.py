import zlib
import base64
import urllib.request
import urllib.parse
import os

plantuml_text = '''@startuml
left to right direction
skinparam packageStyle rectangle

actor Avocat
actor "Secrétaire" as Secretaire

rectangle "Système LexPro" {
  usecase "S'authentifier" as UC_Auth
  usecase "Gérer les clients" as UC_Clients
  usecase "Gérer les dossiers" as UC_Dossiers
  usecase "Gérer les documents" as UC_Docs
  usecase "Gérer les tâches et temps" as UC_Taches
  usecase "Gérer la facturation" as UC_Fact
  usecase "Gérer les rendez-vous" as UC_RDV
}

Avocat --> UC_Auth
Secretaire --> UC_Auth

Avocat --> UC_Clients
Avocat --> UC_Dossiers
Avocat --> UC_Docs
Avocat --> UC_Taches
Avocat --> UC_Fact
Avocat --> UC_RDV

Secretaire --> UC_Clients
Secretaire --> UC_Dossiers
Secretaire --> UC_RDV

UC_Clients ..> UC_Auth : <<include>>
UC_Dossiers ..> UC_Auth : <<include>>
UC_Docs ..> UC_Auth : <<include>>
UC_Taches ..> UC_Auth : <<include>>
UC_Fact ..> UC_Auth : <<include>>
UC_RDV ..> UC_Auth : <<include>>
@enduml'''.encode('utf-8')

encoded = base64.urlsafe_b64encode(zlib.compress(plantuml_text, 9)).decode('utf-8')
url = f"https://kroki.io/plantuml/png/{encoded}"

print("Downloading from:", url)
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
with urllib.request.urlopen(req) as response, open('c:/symfony/Project/LexPro/cas_utilisation.png', 'wb') as out_file:
    out_file.write(response.read())
print("Saved to cas_utilisation.png")
