<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Http\Controllers;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClaudeStreamController extends Controller
{
    public function stream(Request $request, ClaudeManager $manager): StreamedResponse
    {
        $request->validate([
            'prompt' => 'required|string',
            'model' => 'nullable|string',
            'system_prompt' => 'nullable|string',
            'session_id' => 'nullable|string',
        ]);

        $overrides = array_filter([
            'model' => $request->input('model'),
            'system_prompt' => $request->input('system_prompt'),
            'session_id' => $request->input('session_id'),
        ]);

        return new StreamedResponse(function () use ($request, $manager, $overrides) {
            $messages = $manager->query($request->input('prompt'), $overrides);

            foreach ($messages as $message) {
                $text = $message->getTextContent();
                if ($text !== null) {
                    echo "data: " . json_encode(['text' => $text]) . "\n\n";
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                }
            }

            echo "event: done\ndata: {}\n\n";
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
