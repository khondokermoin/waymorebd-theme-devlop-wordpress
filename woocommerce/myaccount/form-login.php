<?php
/**
 * Login / Register — Way More BD (custom 2-column split-screen)
 *
 *   [ Login With Credentials ]   OR   [ Create a New Account ]
 *              — or continue with —  [ social buttons, if a plugin is active ]
 *
 * Standard WooCommerce email/password auth — no phone/OTP dependency.
 * Social buttons render only when a social-login plugin is active
 * (isdb_render_social_login) — otherwise that row is omitted.
 *
 * ── LOAD-BEARING (untouched) ─────────────────────────────────────────────
 *   form.woocommerce-form-login   + name="username" / name="password"
 *   wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce')
 *   button name="login"
 *   form.woocommerce-form-register + name="email" (+ username/password when
 *   the matching auto-generate options are off) + 'woocommerce-register' nonce
 *   button name="register"
 *   Every core do_action(), in core order.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/myaccount/form-login.php
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );

// Capture the actual markup — an installed-but-silent plugin must not leave
// an empty "or continue with" divider behind.
$social_html = function_exists( 'isdb_get_social_login_html' ) ? isdb_get_social_login_html() : '';

$eye_open   = 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z';
$eye_closed = 'M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88';
$ico_user   = 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z';
$ico_lock   = 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z';
$ico_mail   = 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75';
?>

<div class="wmb-account-auth mx-auto max-w-5xl">
	<div class="overflow-hidden rounded-2xl border border-[#e0e0e0] bg-white shadow-sm">
		<div class="p-5 sm:p-8">

			<!-- ── Card head ── -->
			<div class="flex items-center justify-center gap-4">
				<span class="flex h-[58px] w-[58px] flex-none items-center justify-center rounded-2xl bg-brand-primary text-white">
					<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $ico_user ); ?>"/></svg>
				</span>
				<div>
					<h1 class="m-0 text-2xl font-extrabold tracking-tight text-brand-title sm:text-[28px]">Sign in</h1>
					<p class="m-0 mt-0.5 text-sm text-slate-500">Access your account securely</p>
				</div>
			</div>

			<!-- ── Panels: Login | OR | Register ── -->
			<div class="mt-7 grid grid-cols-1 gap-5 <?php echo $registration_enabled ? 'lg:grid-cols-[1fr,auto,1fr]' : ''; ?>">

				<!-- ══════════ LOGIN ══════════ -->
				<div class="rounded-xl bg-[#f7f7f7] p-5 sm:p-6" x-data="{ show:false }">
					<h2 class="m-0 mb-4 text-base font-bold text-brand-title">Login With Credentials</h2>

					<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

						<?php do_action( 'woocommerce_login_form_start' ); ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide relative m-0 mb-3">
							<label for="username" class="sr-only"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?></label>
							<span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
								<svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $ico_user ); ?>"/></svg>
							</span>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text wmb-field pl-11" name="username" id="username" autocomplete="username" placeholder="Email or username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
						</p>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide relative m-0 mb-3">
							<label for="password" class="sr-only"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
							<span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
								<svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $ico_lock ); ?>"/></svg>
							</span>
							<input class="woocommerce-Input woocommerce-Input--text input-text wmb-field px-11" :type="show ? 'text' : 'password'" type="password" name="password" id="password" autocomplete="current-password" placeholder="Password" required aria-required="true" />
							<button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-brand-primary" :aria-label="show ? 'Hide password' : 'Show password'">
								<svg x-show="!show" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $eye_open ); ?>"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
								<svg x-show="show" x-cloak class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $eye_closed ); ?>"/></svg>
							</button>
						</p>

						<?php do_action( 'woocommerce_login_form' ); ?>

						<div class="mb-4 mt-1 flex flex-wrap items-center justify-between gap-2">
							<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme m-0 flex cursor-pointer items-center gap-2 text-[13px] text-brand-body">
								<input class="woocommerce-form__input woocommerce-form__input-checkbox h-4 w-4 rounded border-slate-300 accent-brand-primary" name="rememberme" type="checkbox" id="rememberme" value="forever" />
								<span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
							</label>
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-[13px] font-medium text-brand-primary underline-offset-2 transition hover:underline">
								<?php esc_html_e( 'Forgotten password?', 'woocommerce' ); ?>
							</a>
						</div>

						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

						<button type="submit" class="woocommerce-button button woocommerce-form-login__submit wmb-btn-primary" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
							<?php esc_html_e( 'Login', 'isdb-custom' ); ?>
						</button>

						<?php do_action( 'woocommerce_login_form_end' ); ?>

					</form>
				</div>

				<?php if ( $registration_enabled ) : ?>

					<!-- OR divider -->
					<div class="flex items-center justify-center lg:flex-col">
						<span class="h-px w-full bg-slate-200 lg:h-full lg:w-px"></span>
						<span class="mx-3 flex h-11 w-11 flex-none items-center justify-center rounded-full border border-slate-200 bg-white text-[11px] font-bold uppercase text-slate-400 lg:mx-0 lg:my-3">OR</span>
						<span class="h-px w-full bg-slate-200 lg:h-full lg:w-px"></span>
					</div>

					<!-- ══════════ REGISTER ══════════ -->
					<div class="rounded-xl bg-[#f7f7f7] p-5 sm:p-6" x-data="{ show:false }">
						<h2 class="m-0 mb-4 text-base font-bold text-brand-title">Create a New Account</h2>

						<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

							<?php do_action( 'woocommerce_register_form_start' ); ?>

							<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
								<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide relative m-0 mb-3">
									<label for="reg_username" class="sr-only"><?php esc_html_e( 'Username', 'woocommerce' ); ?></label>
									<span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
										<svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $ico_user ); ?>"/></svg>
									</span>
									<input type="text" class="woocommerce-Input woocommerce-Input--text input-text wmb-field pl-11" name="username" id="reg_username" autocomplete="username" placeholder="Username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
								</p>
							<?php endif; ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide relative m-0 mb-3">
								<label for="reg_email" class="sr-only"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
								<span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
									<svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $ico_mail ); ?>"/></svg>
								</span>
								<input type="email" class="woocommerce-Input woocommerce-Input--text input-text wmb-field pl-11" name="email" id="reg_email" autocomplete="email" placeholder="Email address" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
							</p>

							<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
								<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide relative m-0 mb-3">
									<label for="reg_password" class="sr-only"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
									<span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
										<svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $ico_lock ); ?>"/></svg>
									</span>
									<input type="password" class="woocommerce-Input woocommerce-Input--text input-text wmb-field px-11" :type="show ? 'text' : 'password'" name="password" id="reg_password" autocomplete="new-password" placeholder="Password" required aria-required="true" />
									<button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-brand-primary" :aria-label="show ? 'Hide password' : 'Show password'">
										<svg x-show="!show" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $eye_open ); ?>"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
										<svg x-show="show" x-cloak class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $eye_closed ); ?>"/></svg>
									</button>
								</p>
							<?php else : ?>
								<p class="mb-3 text-[12px] leading-relaxed text-slate-500"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
							<?php endif; ?>

							<?php do_action( 'woocommerce_register_form' ); ?>

							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

							<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit wmb-btn-primary" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
								<?php esc_html_e( 'Create Account', 'isdb-custom' ); ?>
							</button>

							<?php do_action( 'woocommerce_register_form_end' ); ?>

						</form>
					</div>

				<?php endif; ?>
			</div>

			<!-- Social login — rendered only when the plugin actually returns buttons -->
			<?php if ( '' !== $social_html ) : ?>
				<div class="mt-7">
					<div class="mx-auto flex max-w-md items-center gap-3">
						<span class="h-px flex-1 bg-slate-200"></span>
						<span class="text-[13px] text-slate-500">or continue with</span>
						<span class="h-px flex-1 bg-slate-200"></span>
					</div>
					<div class="wmb-social mt-4 flex justify-center">
						<?php echo $social_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin-generated markup. ?>
					</div>
				</div>
			<?php endif; ?>

			<p class="mt-7 text-center text-[13px] text-slate-500">
				By continuing you agree to our
				<a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>" class="font-medium text-brand-primary hover:underline">Terms</a> and
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" class="font-medium text-brand-primary hover:underline">Privacy Policy</a>.
			</p>
		</div>

		<!-- ── Trust strip ── -->
		<div class="border-t border-slate-100 bg-brand-bg px-6 py-4">
			<div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[12px] text-slate-500">
				<span class="inline-flex items-center gap-1.5">
					<svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
					256-bit SSL secured
				</span>
				<span class="inline-flex items-center gap-1.5">
					<svg class="h-4 w-4 text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
					Cash on Delivery
				</span>
				<span class="inline-flex items-center gap-1.5">
					<svg class="h-4 w-4 text-brand-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H9v3h2v7h3v-7h2.5l.5-3h-3V9.5c0-.3.2-.5.5-.5H14z"/></svg>
					<a href="<?php echo esc_url( defined( 'ISDB_FACEBOOK_URL' ) ? ISDB_FACEBOOK_URL : 'https://www.facebook.com/waymore.bd' ); ?>" target="_blank" rel="noopener noreferrer" class="transition hover:text-brand-primary">Message us on Facebook</a>
				</span>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
