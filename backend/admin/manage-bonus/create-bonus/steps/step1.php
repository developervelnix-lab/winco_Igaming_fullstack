<div id="step1" class="wizard-step-content">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Bonus Name <span>*</span></label>
                <input type="text" name="name" class="cus-inp" placeholder="Give it a name..." required value="<?php echo $is_edit ? $bonus_data['name'] : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Bonus Type <span>*</span></label>
                <select name="type" class="cus-sel" required>
                    <option value="" hidden>Select Bonus Type</option>
                    <option value="mass" <?php echo ($is_edit && $bonus_data['type'] == 'mass') ? 'selected' : ''; ?>>Mass</option>
                    <option value="single_account" <?php echo ($is_edit && $bonus_data['type'] == 'single_account') ? 'selected' : ''; ?>>Single Account</option>
                    <option value="single_use_cashback" <?php echo ($is_edit && $bonus_data['type'] == 'single_use_cashback') ? 'selected' : ''; ?>>Cashback</option>
                    <option value="redeposit_bonus" <?php echo ($is_edit && $bonus_data['type'] == 'redeposit_bonus') ? 'selected' : ''; ?>>Redeposit Bonus</option>
                    <option value="multiple_account" <?php echo ($is_edit && $bonus_data['type'] == 'multiple_account') ? 'selected' : ''; ?>>Multiple Account</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Bonus Code</label>
                <input type="text" name="coupon_code" class="cus-inp" placeholder="Optional fixed promo code" value="<?php echo $is_edit ? $bonus_data['coupon_code'] : ''; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Is Published</label>
                <div class="toggle-group">
                    <button type="button" class="toggle-btn <?php echo (!$is_edit || $bonus_data['is_published']) ? 'active' : ''; ?>" data-val="yes">YES</button>
                    <button type="button" class="toggle-btn <?php echo ($is_edit && !$bonus_data['is_published']) ? 'active' : ''; ?>" data-val="no">NO</button>
                    <input type="hidden" name="is_published" value="<?php echo (!$is_edit || $bonus_data['is_published']) ? 'yes' : 'no'; ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Is Public</label>
                <div class="toggle-group">
                    <button type="button" class="toggle-btn <?php echo (!$is_edit || $bonus_data['is_public']) ? 'active' : ''; ?>" data-val="yes">YES</button>
                    <button type="button" class="toggle-btn <?php echo ($is_edit && !$bonus_data['is_public']) ? 'active' : ''; ?>" data-val="no">NO</button>
                    <input type="hidden" name="is_public" value="<?php echo (!$is_edit || $bonus_data['is_public']) ? 'yes' : 'no'; ?>">
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label"> Comment</label>
                <textarea name="comment" class="cus-txt" placeholder="Add administrative notes..."><?php echo $is_edit ? $bonus_data['comment'] : ''; ?></textarea>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <button type="button" class="action-btn next-step" data-next="2">
                Next Step <i class='bx bx-chevron-right'></i>
            </button>
        </div>
</div>
