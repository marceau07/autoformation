import React from 'react';
// Importer depuis react-dom/client pour utiliser createRoot
import { createRoot } from 'react-dom/client';
import '../css/app.css';
import SandboxComponent from './components/SandboxComponent';

// Récupère l'élément où monter l'application
const rootElement = document.getElementById('root');

if (rootElement) {
  // Créer la racine React
  const root = createRoot(rootElement);

  // Utiliser la nouvelle méthode pour rendre ton composant
  root.render(
    <React.StrictMode>
      <SandboxComponent />
    </React.StrictMode>
  );
}