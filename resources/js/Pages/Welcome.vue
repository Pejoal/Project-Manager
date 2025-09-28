<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
});
</script>

<template>
  <Head :title="trans('words.welcome')" />

  <div
    class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 bg-[url('https://laravel.com/assets/img/welcome/background.svg')] bg-center"
  >
    <img
      id="background"
      class="absolute -left-20 top-0 max-w-[877px]"
      src="https://laravel.com/assets/img/welcome/background.svg"
      alt=""
    />
    <div
      class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
    >
      <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
        <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
          <div class="flex lg:justify-center lg:col-start-2">
            <svg
              class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]"
              viewBox="0 0 62 65"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9884L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9884C0.348882 49.9196 0.222437 49.7938 0.138469 49.6282C0.0545019 49.4626 0.0105121 49.3044 0.0121098 49.1337V35.9682L0 35.9546V22.1971C0.00275371 22.1101 0.0146906 22.0234 0.0354718 21.9388C0.0582512 21.8463 0.0914516 21.7561 0.134584 21.6708C0.151718 21.6429 0.1649 21.6129 0.183159 21.5858C0.240477 21.4976 0.319546 21.4267 0.410218 21.381L12.8883 14.6253C12.9423 14.5945 13.0014 14.5749 13.0618 14.5577C13.0845 14.5502 13.1084 14.5364 13.1321 14.5302C13.3014 14.485 13.4782 14.485 13.6475 14.5302C13.6708 14.5377 13.6926 14.5514 13.7144 14.5577C13.777 14.5749 13.8361 14.5945 13.8901 14.6253L25.4519 21.381C25.6043 21.4538 25.7311 21.5792 25.8151 21.7388C25.8991 21.8984 25.9365 22.0808 25.9219 22.2631V35.9682L36.9097 29.4265C36.9423 29.4265 36.9423 29.4265 36.9747 29.4265L49.0482 22.381C49.1023 22.3502 49.1614 22.3306 49.2218 22.3134C49.2445 22.306 49.2684 22.2922 49.2921 22.286C49.4614 22.2408 49.6382 22.2408 49.8075 22.286C49.8308 22.2935 49.8526 22.3072 49.8744 22.3134C49.937 22.3306 49.9961 22.3502 50.0501 22.381L61.5284 29.0614C61.6809 29.1342 61.8077 29.2595 61.8917 29.4191C61.9757 29.5787 62.0131 29.7611 61.9985 29.9434V14.8858C62.0006 14.7978 62.0123 14.7102 62.0353 14.6253H61.8548ZM59.893 17.6116V27.0912L50.6021 32.7367V23.2571L59.893 17.6116ZM47.9149 49.9884V59.468L25.4519 49.9884V40.5089L47.9149 49.9884Z"
                fill="currentColor"
              />
            </svg>
          </div>
          <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
            <Link
              v-if="$page.props.auth.user"
              :href="route('dashboard')"
              class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
              {{ trans('words.dashboard') }}
            </Link>

            <template v-else>
              <Link
                :href="route('login')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
              >
                {{ trans('words.login') }}
              </Link>

              <Link
                v-if="canRegister"
                :href="route('register')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
              >
                {{ trans('words.register') }}
              </Link>
            </template>
          </nav>
        </header>

        <main class="mt-6">
          <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
            <a
              href="https://laravel.com/docs"
              id="docs-card"
              class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
            >
              <div id="screenshot-container" class="relative flex w-full flex-1 items-stretch">
                <img
                  src="https://laravel.com/assets/img/welcome/docs-light.svg"
                  alt="Laravel documentation screenshot"
                  class="aspect-video h-full w-full flex-1 rounded-[10px] object-top object-cover drop-shadow-[0px_4px_34px_rgba(0,0,0,0.06)] dark:hidden"
                />
                <img
                  src="https://laravel.com/assets/img/welcome/docs-dark.svg"
                  alt="Laravel documentation screenshot"
                  class="hidden aspect-video h-full w-full flex-1 rounded-[10px] object-top object-cover drop-shadow-[0px_4px_34px_rgba(0,0,0,0.25)] dark:block"
                />
                <div
                  class="absolute -bottom-16 -left-16 h-40 w-[calc(100%+8rem)] bg-gradient-to-b from-transparent via-white to-white dark:via-zinc-900 dark:to-zinc-900"
                ></div>
              </div>

              <div class="relative flex items-center gap-6 lg:items-end">
                <div id="docs-card-content" class="flex items-start gap-6 lg:flex-col">
                  <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16"
                  >
                    <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <path
                        fill="#FF2D20"
                        d="M23 4a1 1 0 0 0-1.447-.894L12.224 7.77a.5.5 0 0 1-.448 0L2.447 3.106A1 1 0 0 0 1 4v13.382a1.99 1.99 0 0 0 1.105 1.79l9.448 4.728c.14.065.293.1.447.1.154-.005.306-.04.447-.105l9.453-4.724a1.99 1.99 0 0 0 1.1-1.789V4Z"
                      />
                    </svg>
                  </div>

                  <div class="pt-3 sm:pt-5 lg:pt-0">
                    <h2 class="text-xl font-semibold text-black dark:text-white">{{ trans('words.documentation') }}</h2>

                    <p class="mt-4 text-sm/relaxed">
                      {{ trans('words.laravel_documentation_desc') }}
                    </p>
                  </div>
                </div>

                <svg
                  class="size-6 shrink-0 stroke-[#FF2D20]"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                  />
                </svg>
              </div>
            </a>

            <div class="grid gap-6 lg:gap-8">
              <a
                href="https://laracasts.com"
                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
              >
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                  <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <g fill="#FF2D20">
                      <path d="m4 6 8-4 8 4-8 4-8-4ZM2 8l10 5 10-5-10 5L2 8ZM2 16l10 5 10-5-10 5-10-5Z" />
                    </g>
                  </svg>
                </div>

                <div class="pt-3 sm:pt-5">
                  <h2 class="text-xl font-semibold text-black dark:text-white">Laracasts</h2>

                  <p class="mt-4 text-sm/relaxed">
                    {{ trans('words.laracasts_desc') }}
                  </p>
                </div>

                <svg
                  class="size-6 shrink-0 self-center stroke-[#FF2D20]"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                  />
                </svg>
              </a>

              <a
                href="https://laravel-news.com"
                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
              >
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                  <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <g fill="#FF2D20">
                      <path
                        d="M8.75 2.75C9.99264 2.75 11 3.75736 11 5V8.25C11 9.49264 9.99264 10.5 8.75 10.5H5C3.75736 10.5 2.75 9.49264 2.75 8.25V5C2.75 3.75736 3.75736 2.75 5 2.75H8.75Z"
                      />
                      <path
                        d="M19 2.75C20.2426 2.75 21.25 3.75736 21.25 5V8.25C21.25 9.49264 20.2426 10.5 19 10.5H15.25C14.0074 10.5 13 9.49264 13 8.25V5C13 3.75736 14.0074 2.75 15.25 2.75H19Z"
                      />
                      <path
                        d="M8.75 13.5C9.99264 13.5 11 14.5074 11 15.75V19C11 20.2426 9.99264 21.25 8.75 21.25H5C3.75736 21.25 2.75 20.2426 2.75 19V15.75C2.75 14.5074 3.75736 13.5 5 13.5H8.75Z"
                      />
                      <path
                        d="M19 13.5C20.2426 13.5 21.25 14.5074 21.25 15.75V19C21.25 20.2426 20.2426 21.25 19 21.25H15.25C14.0074 21.25 13 20.2426 13 19V15.75C13 14.5074 14.0074 13.5 15.25 13.5H19Z"
                      />
                    </g>
                  </svg>
                </div>

                <div class="pt-3 sm:pt-5">
                  <h2 class="text-xl font-semibold text-black dark:text-white">Laravel News</h2>

                  <p class="mt-4 text-sm/relaxed">
                    {{ trans('words.laravel_news_desc') }}
                  </p>
                </div>

                <svg
                  class="size-6 shrink-0 self-center stroke-[#FF2D20]"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                  />
                </svg>
              </a>
            </div>
          </div>
        </main>

        <footer class="py-16 text-center text-sm text-black dark:text-white/70">
          Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
        </footer>
      </div>
    </div>
  </div>
</template>
