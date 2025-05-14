<?php
session_start();

if(isset($_SESSION['admin_nama']) && isset($_SESSION['admin_id'])) {
  header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>Register Form</title>
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
    <div class="flex max-w-5xl w-full shadow-lg">
      <div class="bg-white px-14 py-12 w-[60%]">
        <h1
          class="helvetica text-5xl tracking-widest mb-8 select-none"
          style="letter-spacing: 0.3em"
        >
          Register
        </h1>
        <form
          class="space-y-6 text-sm"
          action="proses_register.php"
          method="POST"
        >
          <div class="flex gap-5">
            <div class="flex flex-col w-1/2">
              <label class="block helvetica font-bold" for="firstName"
                >Nama Depan<span class="text-red-600">*</span></label
              >
              <input
                required
                class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black pr-8"
                id="firstName"
                name="firstName"
                placeholder="John"
                type="text"
              />
            </div>
            <div class="flex flex-col w-1/2">
              <label class="block helvetica font-bold" for="lastName"
                >Nama Belakang<span class="text-red-600">*</span></label
              >
              <input
                required
                class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black pr-8"
                id="lastName"
                name="lastName"
                placeholder="Doe"
                type="text"
              />
            </div>
          </div>
          <div class="flex flex-col w-3/5">
            <label class="block helvetica font-bold" for="email"
              >Email<span class="text-red-600">*</span></label
            >
            <input
              required
              class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black pr-8"
              id="email"
              name="email"
              placeholder="johndoe@example.com"
              type="email"
            />
          </div>
          <div class="flex gap-5">
            <div class="flex flex-col w-2/4">
              <label class="block helvetica font-bold" for="birthDate"
                >Tanggal Lahir<span class="text-red-600">*</span></label
              >
              <div class="relative w-full max-w-[320px]">
                <span
                  class="absolute left-3 top-1/2 -translate-y-1/2 text-black text-lg"
                >
                </span>
                <input
                  required
                  class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black"
                  id="birthDate"
                  type="date"
                  name="birthDate"
                />
              </div>
            </div>
            <div class="flex flex-col w-2/4">
              <label class="block helvetica font-bold" for="gender"
                >Jenis Kelamin<span class="text-red-600">*</span></label
              >
              <select
                class="w-full rounded-md border border-gray-400 px-3 py-1.5 text-xs placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-black pr-8"
                id="gender"
                name="gender"
              >
                <option disabled="" selected="">Select Gender</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
          </div>
          <div class="flex flex-col w-3/5 relative">
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
          </div>
          <p class="text-[12px] font-bold">
            Sudah malam atau sudah punya akun?
            <a class="underline" href="login.php"> Login </a>
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
      <div class="bg-gradient-to-r from-blue-300 to-white w-[40%]"></div>
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
