# Proiect PHP: Aplicație web de revistă online

⚠️ Under construction ⚠️

## Descriere

Tema proiectului este o aplicație web pentru realizarea unei reviste online.

Aplicația va permite publicarea de articole de către utilizatori autor autorizați,
folosind interfața web, fără a avea nevoie să încarce manual fișiere HTML pe server.
Va exista o interfață user-friendly de redactare a articolelor care permite formatarea textului
și introducerea imaginilor fără HTML. Evident, serverul va genera HTML pentru articole.
Articolele vor avea indicatori de feedback basic - like, dislike, display de număr de vizionări la un articol.

Pentru managementul revistei, va exista un panou de administrator prin care pot fi
modificate permisiunile utilizatorilor, pot fi șterse articole, și pot
fi aprobate editări la articole. Așadar trebuie să existe un tip de utilizator administrator,
pentru a restricționa accesul la sistemul de administrare.

Sistemul ar trebui să fie sigur, și să nu aibă vulerabilități de tip XSS, SQL Injection/Code Injection, CRSF,
stocare nesigură parolelor, utilizare a HTTP fără S sau interfețe de administrare back-end nesecurizate expuse
(i.e. MySQL expus internetului, phpMyAdmin, SSH nesecurizat, etc).

Utilizatorii care s-au înscris și au activat notificări vor primi email când un nou articol este publicat.
Așadar, va trebui instalat un program de trimis mail. Pentru a nu bloca pagina de publicat articol
până când toate emailurile sunt trimise, va trebui ca sistemul să aibă un queue de emailuri netrimise.
Un sistem de trimis mail ar permite și o funcție de resetare a parolei unui cont prin mail.

### Posibile alte feature-uri

Comentarii. Ar necesita o formă mai restricționată de redactare text, posibil permițând italic/bold, dar
nu imagini sau text prea mare. Evident, HTML nu poate fi permis în comentarii. Dacă sunt implementate
comentarii, sistemul va trebui să aibă o modalitate de a șterge comentarii, fie de către autor fie
de către administrator. Un tip de utilizator moderator ar putea fi necesar.

Pt. incluederea elementelor dintr-un site extern - posibil YouTube sau Google Maps, incluse într-un
articol la fel ca o imagine.

## Componente

### Acțiuni posibile

* Vizionare a contului unui utilizator. Dacă are articole, se vor lista. O listă de like și dislike.
* Vizionare a paginii principale. Ar trebui să afișeze cele mai noi articole.
* Vizionare a unui articol. Dacă e prima dată, se va crește numărul de vizionări. Doar autorii au
  access la articole nepublicate pentru a putea vedea un preview.
* Like/dislike al unui articol.
* Creare a unui nou articol. Editare, ștergere, publicare. Dacă articolul nu a fost publicat,
  poate fi editat sau șterg liber, și doar autorul îl poate vedea. După ce e publicat, administratorul
  trebuie să aprobe publicarea unei modificări la articol. Trebuie menținut un istoric al versiunilor.
* Administrare: schimbare a permisiunilor unui cont, aprobare a modificării unui articol.
* Creare a unui cont nou.
* Autentificare.
* Resetare a parolei prin email.
* Exportare a datelor unui cont - va genera o arhivă cu toate articolele asociate, și un raport
  PDF cu lista de like/dislike.

### Componente principale necesare pentru acțiuni

* Un șablon generic de HTML/CSS pentru toate paginile de pe website.
* Un șablon pentru listă de articole care e folosit și pe homepage și pe lista de articole al unui utilizator, și lista de like/dislike.
* Un sistem de generare de articol HTML. Probabil necesită un șablon specializat, și un sistem de rendering.
  Articolul ar putea fi introdus de către autor în format Markdown care va fi convertit in HTML.
  Va fi folosit printr-o interfață de creare și editare de articole.
* Logică de autentificare și autorizare. Validare a sesiunii.
* Trimitere de email-uri, inclusiv unele ce conțin linkuri de autorizare a unei acțiuni i.e. signup, reset password.
* Conectare la baza de date.

## Structura bazei de date

⚠️⚠️⚠️WIP⚠️⚠️⚠️
