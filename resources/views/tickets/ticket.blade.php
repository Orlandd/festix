<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title>
</head>

<body style="margin:0; padding:0; font-family:Arial, sans-serif; background-color:#f4f4f4;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding:20px;">
        <tr>
            <td>
                @foreach ($tickets as $ticket)
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                        style="max-width:600px; background-color:#ffffff; border:1px solid #ddd; border-radius:8px; overflow:hidden; margin-bottom:20px;">
                        <tr>
                            <td>
                                <img src="{{ asset($ticket->eventPrice->event->eventImage[0]->link) }}"
                                    alt="Event Image" style="width:100%; display:block; border-bottom:1px solid #ddd;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:16px;">
                                <h2 style="font-size:20px; color:#4C51BF; margin:0 0 10px;">
                                    {{ $ticket->eventPrice->event->name }}</h2>
                                <p style="font-size:14px; margin:0 0 5px;"><strong>Code:</strong> {{ $ticket->code }}
                                </p>
                                <p style="font-size:14px; margin:0;"><strong>Date:</strong>
                                    {{ $ticket->eventPrice->event->date }}</p>
                                <p style="font-size:14px; margin:0;"><strong>Time:</strong>
                                    {{ $ticket->eventPrice->event->time }}</p>
                                <p style="font-size:14px; margin:0;"><strong>Location:</strong>
                                    {{ $ticket->eventPrice->event->vanue->name }}</p>
                                <hr style="margin:16px 0; border:none; border-top:1px solid #eee;">
                                <p style="font-size:14px; margin:0;"><strong>Category:</strong>
                                    {{ $ticket->eventPrice->seatCategory->name }}</p>
                                <p style="font-size:14px; margin:0 0 10px;"><strong>Seat:</strong>
                                    {{ $ticket->seat_number }}</p>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $ticket->code }}"
                                    alt="QR Code" style="width:100px; height:100px;">
                            </td>
                        </tr>
                    </table>
                @endforeach
            </td>
        </tr>
    </table>
</body>

</html>
