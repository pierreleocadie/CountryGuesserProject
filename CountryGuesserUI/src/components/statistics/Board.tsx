import { Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper } from "@mui/material";

const Board = (props: BoardProps): JSX.Element => {
  return (
    <TableContainer component={Paper}>
      <Table aria-label="simple table">
        <TableHead>
          <TableRow sx={{ bgcolor: "#4BA89C" }}>
            <TableCell sx={{ fontWeight: "bold" }}>Nom du joueur</TableCell>
            <TableCell align="right" sx={{ fontWeight: "bold" }}>Parties jouées</TableCell>
            <TableCell align="right" sx={{ fontWeight: "bold" }}>G/P/Ratio</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {props.rows.slice(0, 10).map((row) => {
            const games_losed = row.games_played - row.games_won;
            const ratio = (row.games_won / (games_losed !== 0 ? games_losed : 1));  // Ratio Gain / Pertes

            return (
            <TableRow
              key={row.nickname}
              sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
            >
              <TableCell component="th" scope="row">
                {row.nickname}
              </TableCell>
              <TableCell align="right">{row.games_played}</TableCell>
              <TableCell align="right">{`${row.games_won}/${games_losed}/${ratio.toFixed(2)}`}</TableCell>
            </TableRow>);
          })}
        </TableBody>
      </Table>
    </TableContainer>
  );
}

interface BoardProps {
  rows: { player_id: number, games_won: number, games_played: number, nickname: string }[];
}

export default Board;