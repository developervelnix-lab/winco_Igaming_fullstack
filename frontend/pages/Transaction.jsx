import Navbar from "../navbar/Navbar";
import TransactionPage from "../sidebar-components/statements/TransactionPage";
import { FaWallet } from "react-icons/fa"; // Importing an icon from react-icons

function Transaction() {
  return (
    <>
      <Navbar />
      <div className="pt-[80px]">
        <TransactionPage />
      </div>
    </>
  );
}

export default Transaction;
