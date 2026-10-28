<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divine — Voices of Scripture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Georgia', 'Cambria', 'Times New Roman', 'serif'],
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        divine: {
                            50: '#f8f7f4',
                            100: '#f0efe9',
                            200: '#e4e2d9',
                            500: '#6b7280',
                            700: '#374151',
                            900: '#1f2937',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', system-ui, sans-serif; }
        .quote-text { font-family: Georgia, 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-[#f7f6f2] min-h-screen text-gray-800">

    {{-- Header --}}
    <header class="sticky top-0 z-50 bg-[#f7f6f2]/80 backdrop-blur-md border-b border-gray-200/60">
        <div class="max-w-2xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold">
                    ✦
                </div>
                <span class="font-semibold text-gray-900 tracking-tight">Divine</span>
            </div>
            <button class="text-sm text-gray-500 hover:text-gray-800 transition-colors px-3 py-1.5 rounded-full hover:bg-gray-200/60">
                Public feed
            </button>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 pb-16">
        {{-- Hero --}}
        <section class="pt-12 pb-8 text-center">
            <p class="text-xs font-medium tracking-[0.2em] text-gray-400 uppercase mb-3">
                Voices of Scripture
            </p>
            <h1 class="text-3xl sm:text-4xl font-serif font-medium text-gray-900 leading-tight mb-4">
                The Bible, quoted<br class="hidden sm:block"> in its own voices.
            </h1>
            <p class="text-sm text-gray-500 max-w-md mx-auto leading-relaxed">
                Every passage is traced to the person behind it — stated in the text,<br class="hidden sm:block">
                carried by tradition, or spoken where no author is named.
            </p>
        </section>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
            <button class="px-3.5 py-1.5 rounded-full text-sm font-medium bg-gray-900 text-white shadow-sm">
                All voices
            </button>
            <button class="px-3.5 py-1.5 rounded-full text-sm font-medium bg-white text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                Stated in text
            </button>
            <button class="px-3.5 py-1.5 rounded-full text-sm font-medium bg-white text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                Traditional
            </button>
            <button class="px-3.5 py-1.5 rounded-full text-sm font-medium bg-white text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                Speaker, not author
            </button>
            <button class="px-3.5 py-1.5 rounded-full text-sm font-medium bg-white text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                Unattributed
            </button>
        </div>

        {{-- Feed --}}
        <div class="space-y-4">

            {{-- Card: Jesus — Matthew 16:13 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        J
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Jesus</span>
                            <span class="text-sm text-gray-400">@jesus</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Matthew 16:13</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    “Who do men say that I, the Son of Man, am?”
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Speaker, not author
                        </span>
                        <span class="text-xs text-gray-400">Spoken by Jesus; the Gospel does not name its own author.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Ethan the Ezrahite --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-teal-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        E
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Ethan the Ezrahite</span>
                            <span class="text-sm text-gray-400">@ethan</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 89:38–45</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    But you have rejected and spurned. You have been angry with your anointed. You have renounced the covenant of your servant. You have defiled his crown in the dust. You have broken down all his hedges. You have brought his strongholds to ruin. All who pass by the way rob him. He has become a reproach to his neighbors. You have exalted the right hand of his adversaries. You have made all of his enemies rejoice. Yes, you turn back the edge of his sword, and haven’t supported him in battle. You have ended his splendor, and thrown his throne down to the ground. You have shortened the days of his youth. You have covered him with shame. Selah.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “A contemplation by Ethan, the Ezrahite.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Isaiah --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-violet-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        I
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Isaiah</span>
                            <span class="text-sm text-gray-400">@isaiah</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Isaiah 40:28–31</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    Haven’t you known? Haven’t you heard? The everlasting God, the LORD, the Creator of the ends of the earth, doesn’t faint. He isn’t weary. His understanding is unsearchable. He gives power to the weak. He increases the strength of him who has no might. Even the youths faint and get weary, and the young men utterly fall; but those who wait for the LORD will renew their strength. They will mount up with wings like eagles. They will run, and not be weary. They will walk, and not faint.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Traditional attribution
                        </span>
                        <span class="text-xs text-gray-400">The book names Isaiah in 1:1. Many scholars date chapters 40 to 55 later.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: King Lemuel --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        K
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">King Lemuel</span>
                            <span class="text-sm text-gray-400">@kinglemuel</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Proverbs 31:8–9</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    Open your mouth for the mute, in the cause of all who are left desolate. Open your mouth, judge righteously, and serve justice to the poor and needy.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Proverbs 31 names King Lemuel. His identity is unknown.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: David — Psalm 25 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        D
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">David</span>
                            <span class="text-sm text-gray-400">@david</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 25:1–7</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    <span class="text-xs font-medium tracking-wide text-gray-400 uppercase block mb-1">By David.</span>
                    To you, LORD, I lift up my soul. My God, I have trusted in you. Don’t let me be shamed. Don’t let my enemies triumph over me. Yes, no one who waits for you will be shamed. They will be shamed who deal treacherously without cause. Show me your ways, LORD. Teach me your paths. Guide me in your truth, and teach me, for you are the God of my salvation. I wait for you all day long. LORD, remember your tender mercies and your loving kindness, for they are from of old times. Don’t remember the sins of my youth, nor my transgressions. Remember me according to your loving kindness, for your goodness’ sake, LORD.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “By David.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Anonymous — Psalm 121 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-slate-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        A
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Anonymous</span>
                            <span class="text-sm text-gray-400">@anonymous</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 121:1–4</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    <span class="text-xs font-medium tracking-wide text-gray-400 uppercase block mb-1">A Song of Ascents.</span>
                    I will lift up my eyes to the hills. Where does my help come from? My help comes from the LORD, who made heaven and earth. He will not allow your foot to be moved. He who keeps you will not slumber. Behold, he who keeps Israel will neither slumber nor sleep.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Unattributed
                        </span>
                        <span class="text-xs text-gray-400">The heading “A Song of Ascents” names no author in the Hebrew text.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Jesus — Mark 6:10–11 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        J
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Jesus</span>
                            <span class="text-sm text-gray-400">@jesus</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Mark 6:10–11</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    Wherever you enter into a house, stay there until you depart from there. Whoever will not receive you nor hear you, as you depart from there, shake off the dust that is under your feet for a testimony against them. Assuredly, I tell you, it will be more tolerable for Sodom and Gomorrah in the day of judgment than for that city!
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Speaker, not author
                        </span>
                        <span class="text-xs text-gray-400">Spoken by Jesus; the Gospel does not name its own author.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Sons of Korah --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-rose-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        S
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Sons of Korah</span>
                            <span class="text-sm text-gray-400">@sonsofkorah</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 88:13–18</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    But to you, LORD, I have cried. In the morning, my prayer comes before you. LORD, why do you reject my soul? Why do you hide your face from me? I am afflicted and ready to die from my youth up. While I suffer your terrors, I am distracted. Your fierce wrath has gone over me. Your terrors have cut me off. They came around me like water all day long. They completely engulfed me. You have put lover and friend far from me, and my friends into darkness.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “A Song. A Psalm by the sons of Korah. For the Chief Musician. To the tune of ‘The Suffering of Affliction.’ A contemplation by Heman, the Ezrahite.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Jeremiah --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-amber-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        J
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Jeremiah</span>
                            <span class="text-sm text-gray-400">@jeremiah</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Jeremiah 29:11–13</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    For I know the thoughts that I think toward you,” says the LORD, “thoughts of peace, and not of evil, to give you hope and a future. You shall call on me, and you shall go and pray to me, and I will listen to you. You shall seek me and find me, when you search for me with all your heart.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">The book opens “The words of Jeremiah.” This is a letter to exiles in Babylon.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Asaph — Psalm 82 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        A
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Asaph</span>
                            <span class="text-sm text-gray-400">@asaph</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 82:1–8</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    <span class="text-xs font-medium tracking-wide text-gray-400 uppercase block mb-1">A Psalm by Asaph.</span>
                    God presides in the great assembly. He judges among the gods. “How long will you judge unjustly, and show partiality to the wicked?” Selah. “Defend the weak, the poor, and the fatherless. Maintain the rights of the poor and oppressed. Rescue the weak and needy. Deliver them out of the hand of the wicked.” They don’t know, neither do they understand. They walk back and forth in darkness. All the foundations of the earth are shaken. I said, “You are gods, all of you are sons of the Most High. Nevertheless you shall die like men, and fall like one of the rulers.” Arise, God, judge the earth, for you inherit all of the nations.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “A Psalm by Asaph.” Echoed by Jesus in John 10:34–36.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: David — Psalm 32 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        D
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">David</span>
                            <span class="text-sm text-gray-400">@david</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 32:7–11</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    You are my hiding place. You will preserve me from trouble. You will surround me with songs of deliverance. Selah. I will instruct you and teach you in the way which you shall go. I will counsel you with my eye on you. Don’t be like the horse, or like the mule, which have no understanding, who are controlled by bit and bridle, or else they will not come near to you. Many sorrows come to the wicked, but loving kindness shall surround him who trusts in the LORD. Be glad in the LORD, and rejoice, you righteous! Shout for joy, all you who are upright in heart!
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “By David. A contemplative psalm.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Asaph — Psalm 80 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        A
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Asaph</span>
                            <span class="text-sm text-gray-400">@asaph</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 80:7–13</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    Turn us again, God of Armies. Cause your face to shine, and we will be saved. You brought a vine out of Egypt. You drove out the nations, and planted it. You cleared the ground for it. It took deep root, and filled the land. The mountains were covered with its shadow. Its boughs were like God’s cedars. It sent out its branches to the sea, its shoots to the River. Why have you broken down its walls, so that all those who pass by the way pluck it? The boar out of the wood ravages it. The wild animals of the field feed on it.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “For the Chief Musician. To the tune of ‘The Lilies of the Covenant.’ A Psalm by Asaph.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Jesus — Luke 21:14–19 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        J
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Jesus</span>
                            <span class="text-sm text-gray-400">@jesus</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Luke 21:14–19</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    Settle it therefore in your hearts not to meditate beforehand how to answer, for I will give you a mouth and wisdom which all your adversaries will not be able to withstand or to contradict. You will be handed over even by parents, brothers, relatives, and friends. They will cause some of you to be put to death. You will be hated by all men for my name’s sake. And not a hair of your head will perish. By your endurance you will win your lives.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Speaker, not author
                        </span>
                        <span class="text-xs text-gray-400">Spoken by Jesus; the Gospel does not name its own author.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Zechariah (prophet) --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-sky-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        Z
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Zechariah (prophet)</span>
                            <span class="text-sm text-gray-400">@zechariahprophet</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Zechariah 9:9</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    Rejoice greatly, daughter of Zion! Shout, daughter of Jerusalem! Behold, your King comes to you! He is righteous, and having salvation; lowly, and riding on a donkey, even on a colt, the foal of a donkey.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Traditional attribution
                        </span>
                        <span class="text-xs text-gray-400">The book names Zechariah in 1:1. Chapters 9 to 14 are often debated.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Solomon — Psalm 72 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-yellow-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        S
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Solomon</span>
                            <span class="text-sm text-gray-400">@solomon</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 72:1–7</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    <span class="text-xs font-medium tracking-wide text-gray-400 uppercase block mb-1">By Solomon.</span>
                    God, give the king your justice; your righteousness to the royal son. He will judge your people with righteousness, and your poor with justice. The mountains shall bring prosperity to the people. The hills bring the fruit of righteousness. He will judge the poor of the people. He will save the children of the needy, and will break the oppressor in pieces. They shall fear you while the sun endures; and as long as the moon, throughout all generations. He will come down like rain on the mown grass, as showers that water the earth. In his days, the righteous shall flourish, and abundance of peace, until the moon is no more.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “By Solomon.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: David — Psalm 59 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        D
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">David</span>
                            <span class="text-sm text-gray-400">@david</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 59:12–17</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    For the sin of their mouth, and the words of their lips, let them be taken in their pride, for the curses and lies which they utter. Consume them in wrath. Consume them, and they will be no more. Let them know that God rules in Jacob, to the ends of the earth. Selah. At evening, let them return. Let them howl like a dog, and go around the city. They shall wander up and down for food, and wait all night if they aren’t satisfied. But I will sing of your strength. Yes, I will sing aloud of your loving kindness in the morning. For you have been my high tower, a refuge in the day of my distress. To you, my strength, I will sing praises. For God is my high tower, the God of my mercy.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “For the Chief Musician. To the tune of ‘Do Not Destroy.’ A poem by David, when Saul sent, and they watched the house to kill him.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Sons of Korah — Psalm 46 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-rose-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        S
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Sons of Korah</span>
                            <span class="text-sm text-gray-400">@sonsofkorah</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 46:1–6</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    <span class="text-xs font-medium tracking-wide text-gray-400 uppercase block mb-1">For the Chief Musician. By the sons of Korah. According to Alamoth.</span>
                    God is our refuge and strength, a very present help in trouble. Therefore we won’t be afraid, though the earth changes, though the mountains are shaken into the heart of the seas; though its waters roar and are troubled, though the mountains tremble with their swelling. Selah. There is a river, the streams of which make the city of God glad, the holy place of the tents of the Most High. God is within her. She shall not be moved. God will help her at dawn. The nations raged. The kingdoms were moved. He lifted his voice and the earth melted.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “For the Chief Musician. By the sons of Korah. According to Alamoth.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Jesus — John 8:14–18 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        J
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Jesus</span>
                            <span class="text-sm text-gray-400">@jesus</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">John 8:14–18</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    “Even if I testify about myself, my testimony is true, for I know where I came from, and where I am going; but you don’t know where I came from, or where I am going. You judge according to the flesh. I judge no one. Even if I do judge, my judgment is true, for I am not alone, but I am with the Father who sent me. It’s also written in your law that the testimony of two people is valid. I am one who testifies about myself, and the Father who sent me testifies about me.”
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Speaker, not author
                        </span>
                        <span class="text-xs text-gray-400">Spoken by Jesus; the Gospel does not name its own author.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Moses --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        M
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Moses</span>
                            <span class="text-sm text-gray-400">@moses</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Psalms 90:1–2</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    <span class="text-xs font-medium tracking-wide text-gray-400 uppercase block mb-1">A Prayer by Moses, the man of God.</span>
                    Lord, you have been our dwelling place for all generations. Before the mountains were born, before you had formed the earth and the world, even from everlasting to everlasting, you are God.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Heading: “A Prayer by Moses, the man of God.”</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Solomon — Ecclesiastes --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-yellow-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        S
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Solomon</span>
                            <span class="text-sm text-gray-400">@solomon</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Ecclesiastes 3:1–4</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    For everything there is a season, and a time for every purpose under heaven: a time to be born, and a time to die; a time to plant, and a time to pluck up that which is planted; a time to kill, and a time to heal; a time to break down, and a time to build up; a time to weep, and a time to laugh; a time to mourn, and a time to dance;
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Traditional attribution
                        </span>
                        <span class="text-xs text-gray-400">The text names “the Preacher, son of David, king in Jerusalem.” Solomon is the traditional identification.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Zechariah (priest) --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-cyan-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        Z
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Zechariah (priest)</span>
                            <span class="text-sm text-gray-400">@zechariahpriest</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Luke 1:68–69</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    “Blessed be the Lord, the God of Israel, for he has visited and redeemed his people; and has raised up a horn of salvation for us in the house of his servant David
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Speaker, not author
                        </span>
                        <span class="text-xs text-gray-400">Spoken by Zechariah, father of John the Baptist, as recorded by Luke.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Jesus — Luke 8:39 --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        J
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Jesus</span>
                            <span class="text-sm text-gray-400">@jesus</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Luke 8:39</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    “Return to your house, and declare what great things God has done for you.”
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Speaker, not author
                        </span>
                        <span class="text-xs text-gray-400">Spoken by Jesus; the Gospel does not name its own author.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

            {{-- Card: Paul --}}
            <article class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-purple-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        P
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-900">Paul</span>
                            <span class="text-sm text-gray-400">@paul</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">Romans 8:38–39</span>
                        </div>
                    </div>
                </div>
                <p class="quote-text text-[15px] leading-relaxed text-gray-800 mb-4">
                    For I am persuaded that neither death, nor life, nor angels, nor principalities, nor things present, nor things to come, nor powers, nor height, nor depth, nor any other created thing will be able to separate us from God’s love which is in Christ Jesus our Lord.
                </p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Stated in text
                        </span>
                        <span class="text-xs text-gray-400">Paul identifies himself as the author of the letter to the Romans.</span>
                    </div>
                    <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-md hover:bg-gray-50">
                        Share
                    </button>
                </div>
            </article>

        </div>
    </main>

    {{-- Optional: simple Alpine.js-style filter interactivity (vanilla JS) --}}
    <script>
        // Filter buttons interactivity
        document.querySelectorAll('main button').forEach(btn => {
            if (btn.textContent.trim() === 'Share') return;
            btn.addEventListener('click', function() {
                // Visual active state only (demo)
                document.querySelectorAll('main > div:nth-child(2) button').forEach(b => {
                    b.classList.remove('bg-gray-900', 'text-white', 'shadow-sm');
                    b.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
                });
                this.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
                this.classList.add('bg-gray-900', 'text-white', 'shadow-sm');
            });
        });
    </script>
</body>
</html>
