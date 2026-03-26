/*
  Author: DevKilla
  Buy Code From: jinkteam.com
  Contact: @devkilla (Telegram)
*/

import { createBrowserRouter, RouterProvider } from 'react-router-dom';
import Home from './components/home/Home';
import GameplayComponent from './components/GamePlayComponent';
import Transaction from './components/pages/Transaction';
import ProfitLossPage from './components/pages/ProfitLossPage';
import OpenBetPage from './components/pages/OpenBetPage';
import ChangePasswordPage from './components/pages/ChangePasswordPage';
import RulesAndRegulationPage from './components/pages/RulesAndRegulationPage';
import ExclusionPolicyPage from './components/pages/ExclusionPolicyPage';
import ResponsibleGamblingPage from './components/pages/ResponsibleGamblingPage';
import PrivacyPolicy from './components/sidebar-components/legal-complience/PrivacyPolicy';
import DepositPage from './components/pages/DepositPage';
import WithdrawPage from './components/pages/WithdrawPage';
import GifrCardPage from './components/pages/GifrCardPage';
import PromotionPage from './components/pages/PromotionPage';
import InviteAndEarnPage from './components/pages/InviteAndEarnPage';
import ContactPage from './components/pages/ContactPage';
import ProtectedRoute from './components/auth/ProtectedRoute';
import ThemeSynchronizer from './constants/ThemeSynchronizer';
import { useEffect } from 'react';
import { useSite, SiteProvider } from './context/SiteContext';
import { URL as BASE_URL } from './utils/constants';

const appRouter = createBrowserRouter([
  {
    path: "/",
    element: <Home/>,
  },
  {
    path: "/withdraw",
    element: (
      <ProtectedRoute>
        <WithdrawPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/deposit",
    element: (
      <ProtectedRoute>
        <DepositPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/game/:gameName",
    element: (
      <ProtectedRoute>
        <GameplayComponent />
      </ProtectedRoute>
    ),
  },
  {
    path: "/game-url/:gameUrl/:gameName",
    element: (
      <ProtectedRoute>
        <GameplayComponent />
      </ProtectedRoute>
    ),
  },
  {
    path: "/transaction",
    element: (
      <ProtectedRoute>
        <Transaction />
      </ProtectedRoute>
    ),
  },
  {
    path: "/betting-profit-loss",
    element: (
      <ProtectedRoute>
        <ProfitLossPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/change-password",
    element: (
      <ProtectedRoute>
        <ChangePasswordPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/openbet",
    element: (
      <ProtectedRoute>
        <OpenBetPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/rules-regulation",
    element: <RulesAndRegulationPage />,
  },
  {
    path: "/exclusion",
    element: <ExclusionPolicyPage />,
  },
  {
    path: "/responsible-gambling",
    element: <ResponsibleGamblingPage />,
  },
  {
    path: "/privacy-policy",
    element: <PrivacyPolicy />,
  },
  {
    path: "/gifrcardreedom",
    element: (
      <ProtectedRoute>
        <GifrCardPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/promotion",
    element: (
      <ProtectedRoute>
        <PromotionPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/inviteandearn",
    element: (
      <ProtectedRoute>
        <InviteAndEarnPage />
      </ProtectedRoute>
    ),
  },
  {
    path: "/contact",
    element: <ContactPage />,
  },
]);

const BrandManager = () => {
  const { accountInfo } = useSite();
  
  useEffect(() => {
    if (accountInfo) {
      if (accountInfo.service_site_name) {
        document.title = accountInfo.service_site_name;
      }
      if (accountInfo.service_site_logo) {
        const favicon = document.querySelector('link[rel="icon"]');
        if (favicon) {
          favicon.href = `${BASE_URL}${encodeURI(accountInfo.service_site_logo)}`;
        }
      }
    }
  }, [accountInfo]);

  return null;
};

function App() {
  return (
    <SiteProvider>
      <BrandManager />
      <ThemeSynchronizer />
      <RouterProvider router={appRouter} />
    </SiteProvider>
  );
}

export default App;
