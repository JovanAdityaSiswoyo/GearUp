// ...existing code...
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('required|string|min:8')]
    public string $password_confirmation = '';

    #[Validate('accepted')]
    public bool $terms = false;

    public function register() {
            $validated = $this->validate();
            \Log::info('Register dipanggil', $validated);
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            \Log::info('User berhasil dibuat', ['user' => $user]);
            Auth::login($user);
            redirect()->route('home');
    }
};
