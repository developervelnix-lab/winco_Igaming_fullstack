<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_settings")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
    exit;
}

// Fetch current values
$settings = [];
$res = mysqli_query($conn, "SELECT * FROM tblservices");
while($row = mysqli_fetch_assoc($res)){
    $settings[$row['tbl_service_name']] = $row['tbl_service_value'];
}

$logo_url = $settings['SITE_LOGO_URL'] ?? 'wincologo.png';
$telegram_url = $settings['TELEGRAM_URL'] ?? '';
$whatsapp_num = $settings['CONTACT_WHATSAPP'] ?? '';
$support_url = $settings['CONTACT_SUPPORT_URL'] ?? '';

$site_address = $settings['SITE_ADDRESS'] ?? '';
$site_tagline = $settings['SITE_TAGLINE'] ?? '';
$site_marquee = $settings['SITE_MARQUEE'] ?? '';
$site_name = $settings['SITE_NAME'] ?? '';
$social_links_json = $settings['SITE_SOCIAL_LINKS'] ?? '[]';
$social_links = json_decode($social_links_json, true) ?: [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title>Site Branding & Assets | <?php echo $APP_NAME; ?></title>
    
    <!-- Custom Brand Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Oswald:wght@400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='../style.css?v=<?php echo time(); ?>' rel='stylesheet'>
    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
    
    <style>
        :root {
            --brand: #E6A000;
            --brand-light: #F2C200;
            --brand-gradient: linear-gradient(135deg, #E6A000 0%, #CC5A00 100%);
            --app-bg: #0D0D0D;
            --panel-bg: #141414;
            --input-bg: #1A1A1A;
            --border-dim: rgba(255,255,255,0.05);
            --text-main: #FFFFFF;
            --text-muted: #7A7A7A;
            --font-display: 'Anton', sans-serif;
            --font-ui: 'Rajdhani', sans-serif;
            --font-head: 'Oswald', sans-serif;
        }

        body {
            background-color: var(--app-bg) !important;
            font-family: var(--font-ui) !important;
            color: var(--text-main) !important;
            margin: 0; padding: 0;
        }

        .branding-container {
            max-width: 1200px; margin: 40px auto; padding: 0 20px;
        }

        .section-header {
            margin-bottom: 30px; border-left: 4px solid var(--brand); padding-left: 20px;
        }
        .section-header h2 {
            font-family: var(--font-display); text-transform: uppercase; font-size: 32px; letter-spacing: 2px; margin: 0;
        }
        .section-header p {
            color: var(--text-muted); font-family: var(--font-head); font-weight: 500; font-size: 14px; margin-top: 5px;
        }

        .asset-card {
            background: var(--panel-bg); border: 1px solid var(--border-dim); border-radius: 24px;
            padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        .card-title {
            font-family: var(--font-head); font-weight: 700; text-transform: uppercase; font-size: 18px;
            color: var(--brand); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;
        }

        .logo-preview-wrapper {
            display: flex; align-items: center; gap: 40px; flex-wrap: wrap;
        }
        .logo-box {
            width: 200px; height: 120px; background: rgba(0,0,0,0.3); border: 1px dashed var(--border-dim);
            border-radius: 16px; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .logo-box img { max-width: 100%; max-height: 100%; object-fit: contain; }

        .upload-controls { flex: 1; min-width: 300px; }
        
        .custom-file-input {
            width: 100%; height: 56px; background: var(--input-bg); border: 1px solid var(--border-dim);
            border-radius: 14px; color: var(--text-main); font-family: var(--font-head); font-weight: 600;
            cursor: pointer; display: flex; align-items: center; padding: 0 20px; position: relative;
        }
        .custom-file-input:hover { border-color: var(--brand); }
        .custom-file-input input { display: none; }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-family: var(--font-head);
        }
        .brand-input {
            width: 100%; height: 52px; background: var(--input-bg); border: 1px solid var(--border-dim);
            border-radius: 12px; padding: 0 16px; color: var(--text-main); font-family: var(--font-ui);
            font-weight: 600; font-size: 15px; transition: all 0.2s;
        }
        .brand-input:focus { border-color: var(--brand); outline: none; box-shadow: 0 0 15px rgba(230,160,0,0.1); }

        .btn-brand-save {
            background: var(--brand-gradient); border: none; border-radius: 14px;
            color: #000; font-family: var(--font-display); font-size: 16px; font-weight: 700;
            padding: 14px 40px; cursor: pointer; transition: all 0.2s; text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-brand-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(230,160,0,0.3); }

        .grid-asset { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        
        .banner-item {
            position: relative; border-radius: 16px; overflow: hidden; aspect-ratio: 16/9;
            border: 1px solid var(--border-dim); background: #000;
        }
        .banner-item img { width: 100%; height: 100%; object-fit: cover; }
        .banner-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0.6); opacity: 0;
            display: flex; align-items: center; justify-content: center; transition: all 0.3s;
        }
        .banner-item:hover .banner-overlay { opacity: 1; }
        
        .btn-circle-action {
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 20px; cursor: pointer; border: none; transition: all 0.2s;
        }
        .btn-delete { background: #FF2D2D; color: #fff; }
        .btn-delete:hover { transform: scale(1.1); }

        .image-preview-float {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            width: 40px; height: 40px; border-radius: 8px; border: 1px solid var(--border-dim);
            overflow: hidden; pointer-events: none;
        }
        .image-preview-float img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

<div class="admin-layout-wrapper">
    <?php include "../components/side-menu.php"; ?>
    
    <div class="admin-main-content hide-native-scrollbar">
        <div class="branding-container">
            
            <div class="section-header">
                <h2>Asset Management</h2>
                <p>Modify site identity, promotional banners, and contact information</p>
            </div>

            <?php if(isset($_GET['msg'])){ ?>
                <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; padding: 15px; border-radius: 12px; margin-bottom: 25px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    <i class='bx bx-check-circle' style="font-size: 20px;"></i>
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php } ?>

            <?php if(isset($_GET['err'])){ ?>
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 15px; border-radius: 12px; margin-bottom: 25px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    <i class='bx bx-error-circle' style="font-size: 20px;"></i>
                    <?php echo htmlspecialchars($_GET['err']); ?>
                </div>
            <?php } ?>

            <!-- SITE LOGO -->
            <form action="manager-branding.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action_type" value="update_logo">
                <div class="asset-card">
                    <div class="card-title"><i class='bx bx-landscape'></i> Site Identity (Logo)</div>
                    <div class="logo-preview-wrapper">
                        <div class="logo-box">
                            <img src="../../<?php echo $logo_url; ?>?v=<?php echo time(); ?>" alt="Site Logo">
                        </div>
                        <div class="upload-controls">
                            <label class="form-label">Upload Brand Logo (PNG Transparent Recommended)</label>
                            <label class="custom-file-input">
                                <span class="file-name-display">Choose Image...</span>
                                <input type="file" name="site_logo" class="crop-upload" data-ratio="NaN" accept="image/*">
                            </label>
                            <p style="color: var(--brand); font-size: 11px; margin-top: 10px; font-weight: bold;">Required minimum size: 240x80px. Format: PNG, JPG.</p>
                        </div>
                        <button type="submit" class="btn-brand-save">Update Logo</button>
                    </div>
                </div>
            </form>

            <!-- CONTACT DETAILS -->
            <form action="manager-branding.php" method="POST">
                <input type="hidden" name="action_type" value="update_contacts">
                <div class="asset-card">
                    <div class="card-title"><i class='bx bx-support'></i> Support & Social Channels</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Telegram URL</label>
                                <input type="text" name="telegram_url" class="brand-input" value="<?php echo $telegram_url; ?>" placeholder="https://t.me/...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" name="whatsapp_num" class="brand-input" value="<?php echo $whatsapp_num; ?>" placeholder="+91 0000 000000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Direct Support Link</label>
                                <input type="text" name="support_url" class="brand-input" value="<?php echo $support_url; ?>" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-brand-save">Save Contact Info</button>
                </div>
            </form>

            <!-- SITE TEXTS -->
            <form action="manager-branding.php" method="POST">
                <input type="hidden" name="action_type" value="update_site_texts">
                <div class="asset-card">
                    <div class="card-title"><i class='bx bx-text'></i> Site Identity Texts</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Site Name (Company Name)</label>
                                <input type="text" name="site_name" class="brand-input" value="<?php echo htmlspecialchars($site_name); ?>" placeholder="e.g. Winco">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tagline (Under Logo)</label>
                                <input type="text" name="site_tagline" class="brand-input" value="<?php echo htmlspecialchars($site_tagline); ?>" placeholder="e.g. Play Win Repeat">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Marquee Text (News Ticker)</label>
                                <input type="text" name="site_marquee" class="brand-input" value="<?php echo htmlspecialchars($site_marquee); ?>" placeholder="Latest news here...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Site Address (Footer)</label>
                                <input type="text" name="site_address" class="brand-input" value="<?php echo htmlspecialchars($site_address); ?>" placeholder="123 Example St...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-brand-save">Save Site Texts</button>
                </div>
            </form>

            <!-- DYNAMIC SOCIAL LINKS -->
            <form action="manager-branding.php" method="POST">
                <input type="hidden" name="action_type" value="update_social_links">
                <div class="asset-card">
                    <div class="card-title" style="justify-content: space-between;">
                        <span><i class='bx bx-share-alt'></i> Dynamic Social Links</span>
                        <button type="button" class="btn-brand-save" style="padding: 8px 20px; font-size: 12px;" onclick="addSocialLink()">+ Add Link</button>
                    </div>
                    <div id="social-links-container">
                        <?php 
                        if(empty($social_links)) {
                            // Default empty row
                            echo '
                            <div class="row social-row mb-3" style="align-items: center;">
                                <div class="col-md-3">
                                    <select name="platforms[]" class="brand-input">
                                        <option value="WhatsApp">WhatsApp</option>
                                        <option value="Telegram">Telegram</option>
                                        <option value="Instagram">Instagram</option>
                                        <option value="Facebook">Facebook</option>
                                        <option value="Twitter">Twitter/X</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="urls[]" class="brand-input" placeholder="Number or URL...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn-circle-action btn-delete" onclick="this.closest(\'.social-row\').remove()"><i class=\'bx bx-x\'></i></button>
                                </div>
                            </div>';
                        } else {
                            foreach($social_links as $link) {
                                $plat = htmlspecialchars($link['platform']);
                                $val = htmlspecialchars($link['value']);
                                $selectOpts = ['WhatsApp', 'Telegram', 'Instagram', 'Facebook', 'Twitter'];
                                $optsHtml = '';
                                foreach($selectOpts as $opt) {
                                    $sel = ($opt === $plat) ? 'selected' : '';
                                    $optsHtml .= "<option value='$opt' $sel>$opt</option>";
                                }
                                echo '
                                <div class="row social-row mb-3" style="align-items: center;">
                                    <div class="col-md-3">
                                        <select name="platforms[]" class="brand-input">'.$optsHtml.'</select>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" name="urls[]" class="brand-input" value="'.$val.'" placeholder="Number or URL...">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn-circle-action btn-delete" onclick="this.closest(\'.social-row\').remove()"><i class=\'bx bx-x\'></i></button>
                                    </div>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    <button type="submit" class="btn-brand-save mt-3">Save Social Links</button>
                </div>
            </form>

            <!-- HOME SLIDERS -->
            <div class="asset-card">
                <div class="card-title" style="justify-content: space-between;">
                    <span><i class='bx bx-images'></i> Homepage Banners (Sliders)</span>
                    <button class="btn-brand-save" style="padding: 8px 20px; font-size: 12px;" onclick="document.getElementById('addSliderModal').style.display='flex'">+ Add Slider</button>
                </div>
                <div class="grid-asset">
                    <?php
                    $sliders = mysqli_query($conn, "SELECT * FROM tblsliders WHERE tbl_slider_status='true'");
                    while($s = mysqli_fetch_assoc($sliders)){
                    ?>
                    <div class="banner-item">
                        <img src="../../<?php echo $s['tbl_slider_img']; ?>" alt="Banner">
                        <div class="banner-overlay">
                            <button class="btn-circle-action btn-delete" onclick="DeleteAsset('slider', <?php echo $s['id']; ?>)"><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- PROMOTIONAL BANNERS -->
            <div class="asset-card">
                <div class="card-title" style="justify-content: space-between;">
                    <span><i class='bx bx-star'></i> Promotional Banners</span>
                    <button class="btn-brand-save" style="padding: 8px 20px; font-size: 12px;" onclick="document.getElementById('addPromoModal').style.display='flex'">+ Add Promo</button>
                </div>
                <div class="grid-asset">
                    <?php
                    $promos = mysqli_query($conn, "SELECT * FROM tbl_promotions WHERE status='true'");
                    while($p = mysqli_fetch_assoc($promos)){
                    ?>
                    <div class="banner-item">
                        <img src="../../<?php echo $p['image_path']; ?>" alt="Promo">
                        <div class="banner-overlay">
                            <button class="btn-circle-action btn-delete" onclick="DeleteAsset('promo', <?php echo $p['id']; ?>)"><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Simple Modal for Adding Assets -->
<div id="addSliderModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:100; align-items:center; justify-content:center; padding:20px;">
    <div class="asset-card" style="width:100%; max-width:500px; margin:0;">
        <div class="card-title">Add Home Slider</div>
        <form action="manager-branding.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action_type" value="add_slider">
            <div class="form-group">
                <label class="form-label">Banner Image <span style="color:var(--brand);">(1200x450px - 16:9)</span></label>
                <input type="file" name="banner_img" class="brand-input crop-upload" data-ratio="2.6666666667" accept="image/*" style="padding:10px;">
            </div>
            <div class="form-group">
                <label class="form-label">Action Link (Optional)</label>
                <input type="text" name="action_url" class="brand-input" placeholder="https://...">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-save">Upload Now</button>
                <button type="button" class="btn-brand-save" style="background:var(--input-bg); color:#fff;" onclick="this.closest('#addSliderModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="addPromoModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:100; align-items:center; justify-content:center; padding:20px;">
    <div class="asset-card" style="width:100%; max-width:500px; margin:0;">
        <div class="card-title">Add Promotional Banner</div>
        <form action="manager-branding.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action_type" value="add_promo">
            <div class="form-group">
                <label class="form-label">Promo Image <span style="color:var(--brand);">(800x400px - 2:1)</span></label>
                <input type="file" name="promo_img" class="brand-input crop-upload" data-ratio="2" accept="image/*" style="padding:10px;">
            </div>
            <div class="form-group">
                <label class="form-label">Action Link (Optional)</label>
                <input type="text" name="action_url" class="brand-input" placeholder="https://...">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-save">Upload now</button>
                <button type="button" class="btn-brand-save" style="background:var(--input-bg); color:#fff;" onclick="this.closest('#addPromoModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function DeleteAsset(type, id) {
        if(confirm("Are you sure you want to delete this asset?")) {
            window.location.href = `manager-branding.php?action_type=delete_asset&type=${type}&id=${id}`;
        }
    }
    
    function addSocialLink() {
        const container = document.getElementById('social-links-container');
        const row = document.createElement('div');
        row.className = 'row social-row mb-3';
        row.style.alignItems = 'center';
        row.innerHTML = `
            <div class="col-md-3">
                <select name="platforms[]" class="brand-input">
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Telegram">Telegram</option>
                    <option value="Instagram">Instagram</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Twitter">Twitter/X</option>
                </select>
            </div>
            <div class="col-md-7">
                <input type="text" name="urls[]" class="brand-input" placeholder="Number or URL...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn-circle-action btn-delete" onclick="this.closest('.social-row').remove()"><i class='bx bx-x'></i></button>
            </div>
        `;
        container.appendChild(row);
    }
</script>

<!-- Crop Modal -->
<div id="cropModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.9); z-index:9999; align-items:center; justify-content:center; padding:20px; flex-direction:column;">
    <div style="background:var(--panel-bg); padding:20px; border-radius:16px; width:100%; max-width:800px; max-height:90vh; display:flex; flex-direction:column; border:1px solid var(--border-dim); box-shadow: 0 10px 40px rgba(0,0,0,0.7);">
        <div class="card-title" style="margin-bottom:15px; color:#fff;">Crop Image</div>
        <div style="flex:1; min-height:300px; max-height:60vh; width:100%; background:#000; overflow:hidden;">
            <img id="cropImage" src="" style="max-width:100%; display:block;">
        </div>
        <div class="d-flex gap-3 justify-content-end mt-4">
            <button type="button" class="btn-brand-save" style="background:#333; color:#fff;" onclick="closeCropModal()">Cancel</button>
            <button type="button" class="btn-brand-save" id="btnApplyCrop">Use This Crop</button>
        </div>
    </div>
</div>

<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
    let currentCropper = null;
    let currentInput = null;
    let currentFileName = "";
    
    document.querySelectorAll('.crop-upload').forEach(input => {
        input.addEventListener('change', function(e) {
            if (!this.files || !this.files[0]) return;
            
            const file = this.files[0];
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                this.value = '';
                return;
            }
            
            currentInput = this;
            currentFileName = file.name;
            const ratio = parseFloat(this.getAttribute('data-ratio')) || NaN;
            
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('cropImage').src = evt.target.result;
                document.getElementById('cropModal').style.display = 'flex';
                
                if (currentCropper) {
                    currentCropper.destroy();
                }
                
                currentCropper = new Cropper(document.getElementById('cropImage'), {
                    aspectRatio: ratio,
                    viewMode: 2,
                    autoCropArea: 1,
                    background: false
                });
            };
            reader.readAsDataURL(file);
        });
    });
    
    function closeCropModal() {
        document.getElementById('cropModal').style.display = 'none';
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
        if (currentInput) {
            currentInput.value = ''; // Reset input to allow choosing the same file again if canceled
        }
    }
    
    document.getElementById('btnApplyCrop').addEventListener('click', function() {
        if (!currentCropper) return;
        
        const canvas = currentCropper.getCroppedCanvas({
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        
        canvas.toBlob(function(blob) {
            const dataTransfer = new DataTransfer();
            const file = new File([blob], currentFileName, { type: 'image/jpeg' });
            dataTransfer.items.add(file);
            currentInput.files = dataTransfer.files;
            
            // update UI label if present
            const labelSpan = currentInput.previousElementSibling;
            if (labelSpan && labelSpan.tagName === 'SPAN') {
                labelSpan.innerText = currentFileName + " (Cropped)";
            } else if (currentInput.parentElement.querySelector('.file-name-display')) {
                currentInput.parentElement.querySelector('.file-name-display').innerText = currentFileName + " (Cropped)";
            }
            
            closeCropModal();
        }, 'image/jpeg', 0.9);
    });
</script>

</body>
</html>
