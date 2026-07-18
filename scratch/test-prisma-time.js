const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()

async function check() {
  try {
    const app = await prisma.appointment.findFirst({
      orderBy: { id: 'desc' }
    })

    if (!app) {
      console.log('No appointments found.')
      return
    }

    console.log('Last Appointment ID:', app.id.toString())
    console.log('date field value:', app.date, 'type:', typeof app.date, 'instanceof Date:', app.date instanceof Date)
    console.log('time field value:', app.time, 'type:', typeof app.time, 'instanceof Date:', app.time instanceof Date)
    
    if (app.time instanceof Date) {
      console.log('time.toISOString():', app.time.toISOString())
      console.log('time.toTimeString():', app.time.toTimeString())
      console.log('time.getUTCHours():', app.time.getUTCHours())
      console.log('time.getUTCMinutes():', app.time.getUTCMinutes())
      console.log('time.getHours():', app.time.getHours())
      console.log('time.getMinutes():', app.time.getMinutes())
    }

    // Test formatTime locally with this db value
    const formatTime = (time) => {
      if (!time) return 'N/A'
      
      let dateObj = null
      if (time instanceof Date) {
        dateObj = time
      } else {
        const parsed = new Date(time)
        if (!isNaN(parsed.getTime())) {
          dateObj = parsed
        } else {
          const match = String(time).match(/^(\d{1,2}):(\d{2})/)
          if (match) {
            return `${match[1].padStart(2, '0')}:${match[2]}`
          }
        }
      }

      if (dateObj) {
        const pad = (n) => n.toString().padStart(2, '0')
        return `${pad(dateObj.getUTCHours())}:${pad(dateObj.getUTCMinutes())}`
      }

      return String(time)
    }

    console.log('Formatted time output:', formatTime(app.time))

  } catch (err) {
    console.error('Error:', err)
  } finally {
    await prisma.$disconnect()
  }
}

check()
