const fs = require('fs');

function fixFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');

  // Insert logic
  if (!content.includes('const openPopup = () => {')) {
    content = content.replace(
      'const [loadingProgress, setLoadingProgress] = useState(0)',
      `const [loadingProgress, setLoadingProgress] = useState(0)

  useEffect(() => {
    const handlePopState = (e) => {
      if (showPopup) {
        setShowPopup(false)
      }
    }
    window.addEventListener("popstate", handlePopState)
    return () => window.removeEventListener("popstate", handlePopState)
  }, [showPopup])

  const openPopup = () => {
    window.history.pushState({ popup: "seeAll" }, "")
    setShowPopup(true)
  }

  const closePopup = () => {
    if (window.history.state && window.history.state.popup === "seeAll") {
      window.history.back()
    } else {
      setShowPopup(false)
    }
  }`
    );
  }

  // Replace usages
  content = content.replace(/if\s*\(\s*showPopup\s*\)\s*\{\s*setShowPopup\(\s*false\s*\)\s*\}/g, 'if (showPopup) {\n        closePopup()\n      }');
  content = content.replace(/onClick=\{\(\) => setShowPopup\(true\)\}/g, 'onClick={openPopup}');
  content = content.replace(/onClick=\{\(\) => setShowPopup\(false\)\}/g, 'onClick={closePopup}');

  fs.writeFileSync(filePath, content);
  console.log('Fixed', filePath);
}

fixFile('src/components/home/Turbogames.jsx');
fixFile('src/components/home/GameDisplay.jsx');
