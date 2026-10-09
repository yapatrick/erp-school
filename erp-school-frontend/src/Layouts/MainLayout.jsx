import React, { useState, useEffect } from "react";
import { Header } from "./MainSection/header";
import { Sidebar } from "./Sidebar";
import { Footer } from "./MainSection/Footer";
import { Outlet } from "react-router-dom";
import "../_CSS/MainLayout.css";

function MainLayout() {
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  return (

  <div className={`app-wrapper ${sidebarCollapsed ? "sidebar-collapse" : ""}`}>

      <Header toggleSidebar={() =>
        setSidebarCollapsed(!sidebarCollapsed)
      } />

      <Sidebar />

      <div className="content-wrapper">

        <Outlet />

      </div>

      <Footer />

    </div>

  );
}

export { MainLayout };