<template>
    <div class="py-16">
        <div class="container">
            <div class="chat-box__wrap h-full md:max-h-[565px]">
                <div class="flex flex-col gap-6 md:flex-row">
                    <div class="chat-list md:min-w-[312px] border border-gray-50 rounded-lg overflow-hidden">
                        <div class="sm:p-6 p-3 border-b border-b-gray-50">
                            <h2>{{ __('message_list') }}</h2>
                        </div>
                        <ul class="pb-4 h-[500px] overflow-y-auto">
                            <li v-for="(user, index) in usersList" :key="index" @click="handleUserClick(user)" class="flex gap-4 items-center justify-between sm:px-6 px-3 py-2 cursor-pointer border-b border-primary-50 " :class="selectedUser.recipient_user_id == user.from_id || selectedUser.recipient_user_id == user.to_id ? 'bg-primary-400 text-white':'hover:bg-primary-50 '">
                                <div v-if="user.to_id == auth.id" class="flex items-center gap-4">
                                    <img class="w-10 h-10 rounded-full" :src="user?.from?.image_url" alt="">
                                    <h3>{{ user?.from?.name }}</h3>
                                </div>
                                <div v-else class="flex items-center gap-4">
                                    <img class="w-10 h-10 rounded-full" :src="user?.to?.image_url" alt="">
                                    <h3>{{ user?.to?.name }}</h3>
                                </div>
                                <p>{{ user?.human_time }}</p>
                            </li>
                        </ul>
                    </div>
                    <div id="chatbox_wrap" class="chat-box border border-gray-50 rounded-lg flex-grow" v-if="selectedUser">
                        <template v-if="auth.id == selectedUser.to_id">
                            <div class="flex gap-4 items-center sm:px-6 px-3 py-3 bg-primary-50">
                                <img class="w-10 h-10 rounded-full" :src="selectedUser?.from?.image_url" :alt="selectedUser?.from?.name">
                                <h3>{{ selectedUser?.from?.name }}</h3>
                                <button v-if="!isBlocked(selectedUser) && !isBlockedBy(selectedUser)" @click="blockUser(selectedUser?.from_id)" class="inline-flex justify-center items-center">
                                    <span class="icon mr-1">
                                        <template>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF0000" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </template>
                                    </span>
                                    {{ __('Block User') }}
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex gap-4 items-center sm:px-6 px-3 py-3 bg-primary-50">
                                <img class="w-10 h-10 rounded-full" :src="selectedUser?.to?.image_url" :alt="selectedUser?.to?.name">
                                <h3>{{ selectedUser?.to?.name }}</h3>
                                <button v-if="!isBlocked(selectedUser) && !isBlockedBy(selectedUser)" @click="blockUser(selectedUser?.to_id)" class="inline-flex justify-center items-center">
                                    <span class="icon mr-1">
                                        <template>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FF0000" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </template>
                                    </span>
                                    {{ __('Block User') }}
                                </button>
                            </div>
                        </template>
                        <div class="h-[405px] overflow-y-auto sm:p-6 p-3" ref="chatbox">
                            <div v-for="(message, index) in messages" :key="index">
                                <div v-if="message.from_id == auth.id" class="send-message flex justify-end mb-3">
                                    <div class="max-w-[70%] flex flex-col gap-1">
                                        <p class="body-xs-400 flex gap-1 items-center text-gray-700">
                                            <span>{{ message.created_time }}</span>
                                        </p>
                                        <a v-if="message.ads_url" :href="message.ads_url" @click="logUrl" class="p-2.5 body-sm-400 text-gray-900 rounded rounded-br-none bg-primary-50">
                                            Ads Url {{ message.ads_url }}
                                        </a>
                                        <p class="p-2.5 body-sm-400 text-gray-900 rounded rounded-br-none bg-primary-50">
                                            {{ message.body }} 
                                        </p>
                                    </div>
                                </div>
                                <div v-else class="receive-message flex justify-start mb-3">
                                    <div v-if="selectedUser.to_id == auth.id" class="max-w-[70%] flex gap-2">
                                        <img class="w-10 h-10 rounded-full object-cover" :src="selectedUser?.from?.image_url" :alt="selectedUser?.from?.name">
                                        <div class="flex flex-col gap-1">
                                            <p class="body-xs-400 flex gap-1 items-center text-gray-700">
                                                <span>{{ selectedUser?.from?.name }}</span>
                                                <span>{{ message.created_time }}</span>
                                            </p>
                                            <a :href="message.ads_url" @click="logUrl" class="p-2.5 body-sm-400 text-gray-900 rounded rounded-br-none bg-primary-50">
                                                {{ message.body }}
                                            </a>
                                        </div>
                                    </div>
                                    <div v-else class="max-w-[70%] flex gap-2">
                                        <img class="w-10 h-10 rounded-full object-cover" :src="selectedUser?.to?.image_url" :alt="selectedUser?.to?.name">
                                        <div class="flex flex-col gap-1">
                                            <p class="body-xs-400 flex gap-1 items-center text-gray-700">
                                                <span>{{ selectedUser?.to?.name }}</span>
                                                <span>{{ message.created_time }}</span>
                                            </p>
                                            <a v-if="message.ads_url" :href="message.ads_url" @click="logUrl" class="p-2.5 body-sm-400 text-gray-900 rounded rounded-br-none bg-primary-50">
                                                Ads Url {{ message.ads_url }}
                                            </a>
                                            <p class="p-2.5 body-sm-400 text-gray-900 rounded rounded-br-none bg-primary-50">
                                                {{ message.body }} 
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form v-if="!isBlocked(selectedUser) && !isBlockedBy(selectedUser)" @submit.prevent="sendMessage" class="w-full sm:p-6 p-3 border-t border-gray-50">
                            <div class="flex gap-4 items-center">
                                <input v-model="message" type="text" placeholder="Type your message..." class="tc-input">
                                <button :disabled="loading || !message.trim()" type="submit" class="btn-primary">
                                    <span class="hidden sm:inline-block">{{ __('send') }}</span>
                                    <loading-icon v-if="loading" />
                                    <send-icon v-else />
                                </button>
                            </div>
                        </form>
                        <div v-else class="w-full sm:p-6 p-3 border-t border-gray-50">
                            <p class="text-red-600">{{ blockMessage }}</p>
                        </div>
                    </div>
                    <div class="chat-box p-12 border border-gray-50 rounded-lg flex justify-center items-center flex-grow" v-else>
                        <div class="text-center flex flex-col justify-center items-center">
                            <NotFoundIcon />
                            <h5 class="mt-4">{{ __('no_message_selected') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import LoadingIcon from './SvgIcon/LoadingIcon.vue';
    import SendIcon from './SvgIcon/SendIcon.vue';
    import NotFoundIcon from './SvgIcon/NotFoundIcon.vue';
    export default {
        components: {
            LoadingIcon,
            SendIcon,
            NotFoundIcon
        },
        props: {
            users: Array,
            auth: Object,
        },
        data() {
            return {
                languageTranslation: [],
                messages: [],
                selectedUser: '',
                selectedUserId: '',
                message: '',
                usersList: this.users,
                loading: false,
                blockedUsers: [],
                blockedByUsers: [],
                blockMessage: ''
            }
        },
        methods: {
            scrollToBottom() {
                this.$nextTick(function () {
                    var container = this.$refs.chatbox;
                    container.scrollTop = container?.scrollHeight;
                });
            },
            async getMessages(user) {
                this.message = ''
                this.messages = []
                this.selectedUser = user

                await this.$nextTick();

                const section = document.getElementById("chatbox_wrap");
                if (section) {
                    window.scroll({
                        behavior: 'smooth',
                        left: 0,
                        top: section.offsetTop
                    });
                }

                let username = user.from_id == this.auth.id ? user?.to?.username : user?.from?.username;

                let response = await axios.get('/dashboard/get/messages/' + username)
                this.messages = response.data

                this.blockMessage = this.isBlocked(user) ? 'You blocked this seller.' : (this.isBlockedBy(user) ? 'You have been blocked by this seller.' : '');
            },
            async handleUserClick(user) {
                this.selectedUser = user;
                await this.getMessages(user);
            },
            async sendMessage(e) {
                if (!this.message.length || this.loading) { return; }
                this.loading = true

                let to_id = this.auth.id == this.selectedUser.to_id ? this.selectedUser.from_id : this.selectedUser.to_id;

                if (!to_id) { alert('User not found'); return; }

                try {
                    let response = await axios.post('/dashboard/send/message', {
                        message: this.message,
                        to: to_id,
                        chat_id: this.selectedUser.id,
                    })

                    this.messages.push(response.data)
                    this.message = ''
                    this.scrollToBottom();
                    this.loading = false
                    this.syncMessageUserList();
                } catch (error) {
                    if (error.response && error.response.status === 403) {
                        alert(error.response.data.error);
                    } else {
                        alert('Something went wrong');
                    }
                    this.loading = false;
                }
            },
            __(key) {
                if (this.languageTranslation) {
                    return this.languageTranslation[key] || key;
                }
                return key;
            },
            async syncMessageUserList(){
                let response = await axios.get('/dashboard/sync/user-list')
                this.usersList = response.data
            },
            playAudio() {
                const sound = new Audio('/frontend/sound.mp3')
                sound.play()
            },
            async fetchTranslateData() {
                let data = await axios.get('/translated/texts');
                this.languageTranslation = data.data
            },
            logUrl() {
                console.log("Message Object:", this.message);
            },
            async blockUser(userId) {
                try {
                    await axios.post('/block-user', {
                        user_id: userId
                    });
                    this.blockedUsers.push(userId);
                    this.blockMessage = 'You have blocked them.';
                    alert('User has been blocked');
                } catch (error) {
                    alert('Something went wrong');
                }
            },
            async fetchBlockedUsers() {
                try {
                    let response = await axios.get('/blocked/users');
                    this.blockedUsers = response.data.blockedUsers;
                    this.blockedByUsers = response.data.blockedByUsers;
                } catch (error) {
                    console.error('Failed to fetch blocked users', error);
                }
            },
            isBlocked(user) {
                let userId = this.auth.id == user.to_id ? user.from_id : user.to_id;
                let blocekd = this.blockedUsers[0] != userId ? false : true ;

                return blocekd;
            },
            isBlockedBy(user) {
                let userId = this.auth.id == user.to_id ? user.from_id : user.to_id;
                let blocekd = this.blockedByUsers[0] != userId ? false : true ;
                return blocekd;
            }
        },
        mounted() {
            this.fetchTranslateData();
            this.fetchBlockedUsers();

            Echo.private('chat')
                .listen('ChatMessage', (e) => {
                    if (e.chatMessage.to_id == this.auth.id) {
                        this.playAudio();
                        this.messages.push(e.chatMessage);
                        this.syncMessageUserList();
                    }
                });
        }
    }
</script>
