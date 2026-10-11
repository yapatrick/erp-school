import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { NavLink } from "react-router-dom";
import "../_CSS/sidebar.css";

function Sidebar() {
     //=======================STATE======================
  const user = useSelector((state) => state.authentication.user);
  const [currentUser, setCurrentUser] = useState(user);

  const [username, setUsername] = useState("");

  const [showAdminBoard, setShowAdminBoard] = useState(false);

  const [showManagerBoard, setShowManagerBoard] = useState(false);

  const [openUsers, setOpenUsers] = useState(false);

  const [openDashboard, setOpenDashboard] = useState(false);

  const [openJournal, setOpenJournal] = useState(false);

  //======================USE EEFFECT===================
  useEffect(() => {
    if (user) {
      setCurrentUser(user.user);

      if (Array.isArray(user.roles) && user.roles.length > 0) {
        const roleName = user.roles[0].nomRole;

        if (roleName) {

          setShowAdminBoard(roleName.includes("Administrateur"));

          setShowManagerBoard(roleName.includes("Manager"));
        }
      }

      console.log("USER:", user);
      console.log("ROLES:", user.roles);
    }
  }, [user, username]);

  //==================INTERFACE GRAPHIOQUE=====================
  return (
    <aside className="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

      {/* Logo */}

    <div className="sidebar-brand-custom">
        <div className="brand-icon">
            <img src="/logo-icon.svg" alt="MITUKAS" />
        </div>
        <div className="brand-text">
            <span className="brand-name">ERP SCHOOL</span>
            <span className="brand-subtitle">Gestion scolaire</span> 
        </div>
    </div>

      {/* Menu */}

      <div className="sidebar-wrapper">
        <nav className="mt-2">
          <ul className="nav sidebar-menu flex-column">

            {/* Pour le profile Administrateur */}
            {showAdminBoard && (

              <React.Fragment>
                <></>
                {/* Page d'accueil  */}

                <li className="nav-item">

                  <NavLink
                    to="/home"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-house-door" />

                    <span>Accueil</span>
                  </NavLink>

                </li>

                {/* Dashboard */}

{/*                 <li className="nav-item">

                  <NavLink
                    to="/dashboardadmin"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-speedometer2" />

                    <span>Dashboard</span>
                  </NavLink>

                </li> */}

                {/* Utilisateurs */}
{/*                 <li className={`nav-item has-treeview ${openUsers ? "menu-open" : ""}`}>
                  <button
                    className="nav-link treeview-btn"
                    onClick={() => setOpenUsers(!openUsers)}
                  >
                    <i className="nav-icon bi bi-people" />

                    <span className="menu-text">
                      Utilisateurs
                    </span>

                    <i
                      className={`bi bi-angle-left nav-arrow ${openUsers ? "rotate" : ""
                        }`}
                    ></i>

                  </button>

                  <ul
                    className={`nav nav-treeview ${openUsers ? "show" : ""
                      }`}
                  >

                    <li className="nav-item">

                      <NavLink
                        to="/listuserroles"
                        className="nav-link"
                      >
                        <i className="nav-icon bi bi-user"></i>

                        <span>Users with roles</span>
                      </NavLink>

                    </li>

                    <li className="nav-item">

                      <NavLink
                        to="/inactifsusers"
                        className="nav-link"
                      >
                        <i className="nav-icon bi bi-tachometer-alt nav"></i>

                        <span>Users inactifs</span>
                      </NavLink>

                    </li>

                    <li className="nav-item">

                      <NavLink
                        to="/profile"
                        className="nav-link"
                      >
                        <i className="nav-icon bi bi-users"></i>

                        <span>Profile utilisateur</span>
                      </NavLink>
                    </li>

                  </ul>
                </li> */}

                {/* Roles */}
{/*                 <li className="nav-item">

                  <NavLink
                    to="/fetchallroles"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-book-open"></i>

                    <span>Rôles</span>
                  </NavLink>

                </li> */}

                {/* Sécurité */}
{/*                 <li className={`nav-item has-treeview ${openJournal ? "menu-open" : ""}`}>
                  <button
                    className="nav-link treeview-btn"
                    onClick={() => setOpenJournal(!openJournal)}
                  >
                    <i className="nav-icon bi bi-cogs"></i>

                    <span className="menu-text">
                      Sécurité
                    </span>

                    <i
                      className={`bi bi-angle-left nav-arrow ${openJournal ? "rotate" : ""
                        }`}
                    ></i>

                  </button>
                  <ul
                    className={`nav nav-treeview ${openJournal ? "show" : ""
                      }`}
                  >

                    <li className="nav-item">

                      <NavLink
                        to="/fetchallevents"
                        className="nav-link"
                      >
                        <i className="nav-icon bi bi-book"></i>

                        <span>Journal des évenements</span>
                      </NavLink>

                    </li>

                  </ul>
                </li> */}

              </React.Fragment>

            )}

            {/* Pour le profile Manager */}
            {showManagerBoard && (
              <React.Fragment>
                <></>
                {/* DashBoard Manager */}

                <li className="nav-item">

                  <NavLink
                    to="#"
                    className="nav-link"
                  >
                    <i className="bi bi-speedometer2" />
                    <span>Tableau de bord</span>
                  </NavLink>

                </li>

                {/* Rapports  */}

                <li className="nav-item">
                  <NavLink
                    to="#"
                    className="nav-link"
                  >
                    <i className="bi bi-file-earmark-bar-graph" />
                    <span>Rapports</span>
                  </NavLink>
                </li>

                {/* Etudiants */}

{/*                 <li className="nav-item">

                  <NavLink
                    to="/fetchallchauffeurs"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-person-vcard" />

                    <span>Chauffeurs</span>

                  </NavLink>

                </li> */}

                {/* Inscriptions */}

{/*                 <li className="nav-item">

                  <NavLink
                    to="/fetchAllVehicules"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-car-front" />

                    <span>Véhicules</span>

                  </NavLink>

                </li> */}

                {/* A */}
                <li className="nav-item">
                    <NavLink to="/vidanges" className="nav-link">
                        <i className="bi bi-droplet-fill" />
                        <span>Vidanges</span>
                    </NavLink>
                </li>

                {/*Recettes*/}

                <li className="nav-item">

                  <NavLink
                    to="#"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-cash-stack" />

                    <span>Notes</span>

                  </NavLink>

                </li>

                {/* Dépenses */}
                <li className="nav-item">

                  <NavLink
                    to="#"
                    className="nav-link"
                  >
                    <i className="nav-icon bi bi-receipt" />

                    <span>Professeurs</span>

                  </NavLink>

                </li>

                {/* Paramètres */}

                <li className={`nav-item has-treeview ${openJournal ? "menu-open" : ""}`}>

                  <button
                    className="nav-link treeview-btn"
                    onClick={() => setOpenJournal(!openJournal)}
                  >

                    <i className="nav-icon bi bi-gear" />

                    <span className="menu-text">
                      Paramètres
                    </span>

                    <i
                      className={`bi bi-angle-left nav-arrow ${openJournal ? "rotate" : ""
                        }`}
                    ></i>

                  </button>

                  <ul
                    className={`nav nav-treeview ${openJournal ? "show" : ""
                      }`}
                  >

                    <li className="nav-item">

                      <NavLink
                        to="/fetchallpays"
                        className="nav-link"
                      >
                        <i className="far fa-circle nav-icon"></i>

                        <span>Pays</span>

                      </NavLink>

                    </li>

                    <li className="nav-item">

                      <NavLink
                        to="/fetchallconstructeurs"
                        className="nav-link"
                      >
                        <i className="far fa-circle nav-icon"></i>

                        <span>Constructeurs</span>

                      </NavLink>

                    </li>

                    <li className="nav-item">

                      <NavLink
                        to="/fetchallcategories"
                        className="nav-link"
                      >
                        <i className="far fa-circle nav-icon"></i>

                        <span>Catégories véhicules</span>

                      </NavLink>

                    </li>

                     <li className="nav-item">

                      <NavLink
                        to="/fetchallclasses"
                        className="nav-link"
                      >
                        <i className="bi bi-vehicles nav-icon"></i>

                        <span>Classes</span>

                      </NavLink>

                    </li> 


                    <li className="nav-item">

                      <NavLink
                        to="/fetchallmodeles"
                        className="nav-link"
                      >
                        <i className="bi bi-home nav-icon"></i>

                        <span>Modeles</span>

                      </NavLink>

                    </li>

                  </ul>

                </li>
              </React.Fragment>
            )}
          </ul>

        </nav>
      </div>
    </aside>
  );
}

export  { Sidebar }