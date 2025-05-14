<?php
session_start();

if(isset($_SESSION['admin_nama']) && isset($_SESSION['admin_id'])) {
  header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body
    class="min-h-screen flex items-center justify-center"
    style="background: linear-gradient(135deg, #fef6f0 0%, #f98f3e 100%)"
  >
    <div class="flex w-full max-w-3xl bg-white">
      <div class="w-1/2 p-8">
        <h1
          class="helvetica text-5xl tracking-widest mb-8 select-none"
          style="letter-spacing: 0.3em"
        >
          Login
        </h1>
        <form class="space-y-4 text-sm" action="proses_login.php" method="POST">
          <label class="block helvetica font-bold" for="email"
            >Email<span class="text-red-600">*</span></label
          >
          <input
            required
            id="email"
            type="email"
            name="email"
            placeholder="johndoe@example.com"
            class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black"
          />

          <label class="block helvetica font-bold" for="password"
            >Password<span class="text-red-600">*</span></label
          >
          <div class="relative">
            <input
              required
              id="password"
              type="password"
              name="password"
              minlength="8"
              placeholder="Min. 8 character"
              class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black pr-8"
            />
            <i
              class="fas fa-eye absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 cursor-pointer"
              aria-hidden="true"
            ></i>
          </div>

          <p class="text-[12px] font-bold">
            Tidak punya akun?
            <a href="register.php" class="underline">Register</a>
          </p>

          <div class="flex space-x-4 mt-10">
            <button
              type="submit"
              class="bg-black text-white rounded-md px-6 py-2 text-sm font-semibold"
            >
              Submit
            </button>
            <button
              type="reset"
              class="bg-gray-600 text-white rounded-md px-6 py-2 text-sm font-semibold"
            >
              Reset
            </button>
          </div>
        </form>
      </div>
      <div class="w-1/2 bg-linear-to-r from-yellow-300 to-white"></div>
    </div>
    <script>
      const passwordinput = document.getElementById("password");
      const eyeIcon = document.querySelector(".fa-eye");

      eyeIcon.addEventListener("click", () => {
        if (passwordinput.type === "password") {
          passwordinput.type = "text";
          eyeIcon.classList.remove("fa-eye");
          eyeIcon.classList.add("fa-eye-slash");
        } else {
          passwordinput.type = "password";
          eyeIcon.classList.remove("fa-eye-slash");
          eyeIcon.classList.add("fa-eye");
        }
      });

      const params = new URLSearchParams(window.location.search);
      const error = params.get("error");
      if (error) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: decodeURIComponent(error),
          confirmButtonColor: "#d33",
        });
      }
    </script>
  </body>
</html>
