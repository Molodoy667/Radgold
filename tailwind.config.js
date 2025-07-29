/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js"
  ],
  mode: 'jit',
  rtl: true,
  theme: {
    extend: {
        colors: {
            gray: {
                50: "var(--gray-50)",
                100: "var(--gray-100)",
                200: "var(--gray-200)",
                300: "var(--gray-300)",
                400: "var(--gray-400)",
                500: "var(--gray-500)",
                600: "var(--gray-600)",
                700: "var(--gray-700)",
                800: "var(--gray-800)",
                900: "var(--gray-900)",
            },
            primary: {
                50: "var(--primary-50)",
                100: "var(--primary-100)",
                200: "var(--primary-200)",
                300: "var(--primary-300)",
                400: "var(--primary-400)",
                500: "var(--primary-500)",
                600: "var(--primary-600)",
                700: "var(--primary-700)",
                800: "var(--primary-800)",
                900: "var(--primary-900)",
            },
            success: {
                50: "#EAF2E8",
                100: "#D5E4D1",
                200: "#ABCAA4",
                300: "#82AF76",
                400: "#589549",
                500: "#2E7A1B",
                600: "#256216",
                700: "#1C4910",
                800: "#12310B",
                900: "#091805",
            },
            warning: {
                50: "#FCEFE7",
                100: "#FAE0CE",
                200: "#F5C09D",
                300: "#EFA16D",
                400: "#EA813C",
                500: "#E5620B",
                600: "#B74E09",
                700: "#893B07",
                800: "#5C2704",
                900: "#2E1402",
            },
            error: {
                50: "#FAEAEA",
                100: "#F5D4D4",
                200: "#EBAAAA",
                300: "#E17F7F",
                400: "#D75555",
                500: "#CD2A2A",
                600: "#A42222",
                700: "#7B1919",
                800: "#521111",
                900: "#290808",
            },
        }
    },
    fontFamily: {
      display: "'Inter', sans-serif",
    },
    container: {
      center: true,
      padding: "1rem",
      screens: {
        "2xl": "1320px",
      },
    },
  },
  plugins: [
    require('flowbite/plugin')({
        charts: true,
    })
  ],
}

