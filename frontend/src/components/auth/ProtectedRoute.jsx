import React, { useEffect } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useSite } from '../../context/SiteContext';

const ProtectedRoute = ({ children }) => {
  const authSecretKey = sessionStorage.getItem('auth_secret_key');
  const { setShowLogin } = useSite();
  const location = useLocation();

  useEffect(() => {
    if (!authSecretKey) {
      setShowLogin(true);
    }
  }, [authSecretKey, setShowLogin]);

  if (!authSecretKey) {
    return <Navigate to="/" state={{ from: location }} replace />;
  }
  
  return children;
};

export default ProtectedRoute;
