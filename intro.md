# Proiect PHP: Aplicație web de revistă online

⚠️ Under construction ⚠️

## Descriere

Tema proiectului este o aplicație web pentru realizarea unei reviste online.

Aplicația va permite publicarea de articole de către utilizatori autorizați,
folosind interfața web, fără a avea nevoie să încarce manual fișiere HTML pe server.
Va exista o interfață user-friendly de redactare a articolelor care permite formatarea textului
și introducerea imaginilor fără HTML. Evident, serverul va genera HTML pentru articole.
Articolele vor avea indicatori de feedback basic - like, dislike, display de număr de vizionări la un articol.

Pentru managementul revistei, va exista un panou de administrator prin care pot fi
modificate permisiunile utilizatorilor, pot fi șterse articole, și pot
fi aprobate ștergeri sau editări la articole. Așadar trebuie să existe un tip de utilizator administrator,
pentru a restricționa accesul la sistemul de administrare.

Sistemul ar trebui să fie sigur, și să nu aibă vulerabilități de tip XSS, SQL Injection/Code Injection, CRSF,
stocare nesigură parolelor, utilizare a HTTP fără S sau interfețe de administrare back-end nesecurizate expuse
(i.e. MySQL expus internetului, phpMyAdmin, SSH nesecurizat, etc).

### Posibile alte feature-uri

Utilizatorii care s-au înscris și au activat notificări vor primi email când un nou articol este publicat.
Așadar, va trebui instalat un program de trimis mail. Pentru a nu bloca pagina de publicat articol
până când toate emailurile sunt trimise, va trebui ca sistemul să aibă un queue de emailuri netrimise.

Comentarii. Ar necesita o formă mai restricționată de redactare text, posibil permițând italic/bold, dar
nu imagini sau text prea mare. Evident, HTML nu poate fi permis în comentarii. Dacă sunt implementate
comentarii, sistemul va trebui să aibă o modalitate de a șterge comentarii, fie de către autor fie
de către administrator. Un tip de utilizator moderator ar putea fi necesar.
