import React from 'react'
import { createBrowserRouter, RouterProvider } from "react-router-dom";

const router = createBrowserRouter([
  /**
   * Routes publiques
   * Ces routes sont accessibles à tous les utilisateurs, même ceux qui ne sont pas connectés.
   * Elles incluent généralement des pages comme la page de connexion, la page d'inscription, etc.  
   */

  {
    path: "/",
    element: <Login />,
  },
  {
    path: "/login",
    element: <Login />,
  },


  /***
  Routes protégées - à ajouter une fois que le système d'authentification est en place. 
**/


]);

function Routes() {
  return (
    <RouterProvider router={router} />
  );
}

export  {Routes}