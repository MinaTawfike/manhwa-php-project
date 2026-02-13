


use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

public function register()
{
    $this->reportable(function (Throwable $e) {
        Log::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    });

    $this->renderable(function (Throwable $e, $request) {
        // Redirect ALL web requests to friendly page
        if ($request->wantsJson() === false) {
            return redirect()->route('error.page')
                ->with('error', 'Oops! Something went wrong. Please try again.');
        }
    });
}


