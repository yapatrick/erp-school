import React, { useState, useEffect, useMemo } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { authHeader } from '../../_helpers/auth-header.jsx';
import { API_URL } from '../../_api/ApiUrl';

function Header() {
   const [openDropdown, setOpenDropdown] = useState(false);
    const auth = useSelector((state) => state.authentication);
    console.log("Redux auth state:", auth); // Vérifiez la structure complète
    const user = useSelector((state) => state.authentication.user);
    const [currentUser, setCurrentUser] = useState(user);
    const [nomRole, setNomRole] = useState("");
    const [id, setId] = useState("0");
    const [firstname, setFirstname] = useState("");
    const [lastname, setLastname] = useState("");
    const [username, setUsername] = useState("");

    const navigate = useNavigate();
    const dispatch = useDispatch();

    //UseRole data array
    const [userData, setUserData] = useState([]);

    //Au chargement de la page
    useEffect(() => {
        if (user?.user) {
            const u = user.user;

            setCurrentUser(u);
            setId(u.id);
            setFirstname(u.firstname);
            setLastname(u.lastname);
            setUsername(u.username);

            fetchUsersWithRole(u.id); // ✅ bon id
        }
    }, [user]);

    async function fetchUsersWithRole(id) {
        try {
            const res = await axios.get(API_URL + `user/${id}`, {
                withCredentials: 'true',
                headers: {
                    ...authHeader(),
                    Accept: 'application/json',
                    'Content-Type': 'application/json'
                },
            });

            const userData = res.data || res.data.data || { };

            console.log("UserData:", userData);

            if (userData.data.roles && userData.data.roles.length > 0) {
                //setNomRole(userData.roles[0].nomRole);
                setNomRole(userData.data.roles[0].nomRole);
            } else {
                setNomRole("Aucun rôle");
            }

        } catch (e) {
            console.error(e);
        }
    }


    // Fonction logout
    const handleLogout = async () => {
        try {
            const response = await axios.post(
                API_URL + 'logout/jwt',
                {},
                {
                    headers: {
                        ...authHeader(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            console.log('Déconnexion réussie:', response.data);

            // Supprimer les données d'authentification du localStorage
            localStorage.removeItem('user');
            localStorage.removeItem('token');

            // Rediriger vers la page de connexion
            navigate('/login');

        } catch (err) {
            console.error('Erreur lors de la déconnexion:', err);

            // Même en cas d'erreur, on déconnecte côté frontend
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            navigate('/login');
        }
    };

    //Affichage de la page UI
    return (
        <nav className="app-header navbar navbar-expand">

            <button
                type="button"
                className="sidebar-toggle-btn"
                onClick={toggleSidebar}
                aria-label="Afficher ou masquer le menu"
            >
                <i className="bi bi-list" />
            </button>


            {/* Right navbar links */}
            <ul className="navbar-nav ms-auto align-items-center">
                {/* ====== 🔔 CLOCHE DE NOTIFICATIONS ====== */}
                <li className="nav-item me-2" style={{ display: 'flex', alignItems: 'center' }}>
                    <NotificationsBell />
                </li>
                {/* ====== INFOS UTILISATEUR ====== */}
                <div className="user-info me-3">
                    
                    Bon retour {firstname} {lastname}! &nbsp;&nbsp; Vous avez le rôle {" "}
                    <span className="user-role" style={{ color: "#FFFFFF", fontWeight: "bold" }}>
                        {nomRole}
                    </span>
                </div>
                &nbsp;&nbsp;

                {currentUser ? (

                    <li className="nav-item dropdown">

                        <div className="dropdown-user position-relative">

                            <button
                                type="button"
                                className="profile-trigger"
                                onClick={() => setOpenDropdown((open) => !open)}
                                aria-expanded={openDropdown}
                                aria-label="Ouvrir le menu utilisateur"
                            >
                                <i className="bi bi-person-circle" />
                            </button>

                            {openDropdown && (

                                <div className="custom-dropdown">

                                    <a href="/profile">
                                        Mon profil
                                    </a>

                                    <a href="/parametrage">
                                        Paramétrage
                                    </a>

                                    <button onClick={handleLogout}>
                                        Déconnexion
                                    </button>

                                </div>

                            )}

                        </div>

                    </li>
                ) : (
                    <a href={"/login"} className="nav-link">
                        Login
                    </a>
                )}
            </ul>
        </nav>
    );
}

export  { Header }