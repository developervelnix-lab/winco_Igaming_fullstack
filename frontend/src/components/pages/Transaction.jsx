import Navbar from "../navbar/Navbar";
import TransactionPage from "../sidebar-components/statements/TransactionPage";
import { useColors } from '../../hooks/useColors';

function Transaction() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className="pt-[140px] md:pt-[160px] pb-10 px-2">
        <TransactionPage />
      </div>
    </div>
  );
}

export default Transaction;
