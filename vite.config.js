import { defineConfig, loadEnv } from "vite";
import path from "path";
import fs from "fs";
import obfuscatorPlugin from "vite-plugin-javascript-obfuscator";
import tailwindcss from "@tailwindcss/vite";
import { globSync } from "glob";

export default defineConfig(({ mode }) => {
  // Load variabel environment dari file di root direktori (.env / env)
  // Parameter ketiga "" memastikan semua variabel di-load tanpa memedulikan prefix
  const env = loadEnv(mode, process.cwd(), "");

  return {
    build: {
      outDir: "public/build",
      emptyOutDir: true,
      manifest: true,
      rollupOptions: {
        input: {
          main: path.resolve(__dirname, "resources/js/main.js"),
          "resources/css/main": path.resolve(
            __dirname,
            "resources/css/main.css",
          ),
          // Membaca semua file .js, .ts, .css, .scss di dalam app/Views
          ...globSync("app/Views/**/*.{js,ts,css,scss}").reduce(
            (entries, file) => {
              // Gunakan nama file (tanpa ekstensi) sebagai nama entry / key
              const entryName = file.replace(/\.[^/.]+$/, "");
              entries[entryName] = path.resolve(__dirname, file);
              return entries;
            },
            {},
          ),
        },
        output: {
          entryFileNames: "assets/[hash].js",
          chunkFileNames: "assets/[hash].js",
          assetFileNames: "assets/[hash].[ext]",
        },
      },
    },
    server: {
      host: env["vite.host"] || "127.0.0.1",
      port: parseInt(env["vite.port"]) || 5173,
    },
    plugins: [
      tailwindcss(),
      obfuscatorPlugin({
        include: ["app/Views/**/*.js"],
        exclude: [/node_modules/],
        apply: "build",
        debugger: true,
        options: {
          compact: true,
          controlFlowFlattening: true,
          controlFlowFlatteningThreshold: 0.75,
          deadCodeInjection: true,
          deadCodeInjectionThreshold: 0.4,
          debugProtection: false,
          debugProtectionInterval: 0,
          disableConsoleOutput: false,
          identifierNamesGenerator: "hexadecimal",
          log: false,
          numbersToExpressions: true,
          renameGlobals: false,
          selfDefending: false,
          simplify: true,
          splitStrings: true,
          splitStringsChunkLength: 10,
          stringArray: true,
          stringArrayCallsTransform: true,
          stringArrayCallsTransformThreshold: 0.5,
          stringArrayEncoding: ["base64"],
          stringArrayIndexShift: true,
          stringArrayRotate: true,
          stringArrayShuffle: true,
          stringArrayWrappersCount: 1,
          stringArrayWrappersChainedCalls: true,
          stringArrayWrappersParametersMaxCount: 2,
          stringArrayWrappersType: "variable",
          stringArrayThreshold: 0.75,
          unicodeEscapeSequence: false,
        },
      }),
      {
        name: "vite-hot-file",
        apply: "serve",
        configureServer(server) {
          const hotFile = path.resolve(__dirname, "public/hot");
          const host = server.config.server.host || "127.0.0.1";
          const port = server.config.server.port || 5173;
          const url = `http://${host}:${port}`;

          fs.writeFileSync(hotFile, url);

          const cleanup = () => {
            try {
              if (fs.existsSync(hotFile)) {
                fs.unlinkSync(hotFile);
              }
            } catch (err) {}
          };

          server.httpServer.once("close", cleanup);
          ["SIGINT", "SIGTERM", "exit"].forEach((signal) => {
            process.on(signal, () => {
              cleanup();
              if (signal !== "exit") {
                process.exit();
              }
            });
          });
        },
      },
    ],
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "resources"),
      },
    },
  };
});
